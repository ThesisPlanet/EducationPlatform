<?php
/**
 * Thesis Planet - Digital Education Platform
 *
 * LICENSE
 *
 * This source file is subject to the licensing terms found at http://www.thesisplanet.com/platform/tos
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to sales@thesisplanet.com so we can send you a copy immediately.
 *
 * @category  ThesisPlanet
 * @copyright  Copyright (c) 2009-2012 Thesis Planet, LLC. All Rights Reserved. (http://www.thesisplanet.com)
 * @license   http://www.thesisplanet.com/platform/tos   ** DUAL LICENSED **  #1 - Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License. #2 - Thesis Planet Commercial Use EULA.
 */
namespace App\Service\Content;

class Audio extends \App\Service\aContent implements \App\Service\Content\iAudio
{

    protected $_repository = null;

    protected $_courseRepository = null;

    protected $_form = null;

    protected $_deleteForm = null;

    public function __construct ($options = array())
    {
        $this->_em = \Zend_Registry::get('em');
        $this->_repository = $this->_em->getRepository(
                '\App\Entity\Content\Audio');
        $this->_courseRepository = $this->_em->getRepository(
                '\App\Entity\Course');
        $this->_acl = new \App\Service\ACL\Content\Audio();
    }

    public function find ($id)
    {
        return $this->_repository->find($id);
    }

    public function findByCourseId ($courseId)
    {
        return $this->_repository->findBy(
                array(
                        '_course' => $courseId
                ));
    }

    public function acl_create (array $data)
    {
        // moving permissions down & providing the is_allowed method the new
        // object (yet to be created).
        if (isset($data['course'])) {
            $courseObj = $this->_courseRepository->find($data['course']);
            $courseService = new \App\Service\Course();
            $courseService->setUser($this->_user);
            if (! $courseService->isAllowed($courseObj, 'acl_manageContent')) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }

        $form = $this->getForm();
        $form->setSubmitLabel("Create");
        if ($form->isValid($data)) {
            $obj = new \App\Entity\Content\Audio();
            $obj->setTitle($data['title']);
            $obj->setDescription($data['description']);
            $obj->setCourse($this->_courseRepository->find($data['course']));
            $obj->setIsPublished($data['isPublished']);
            $obj->setIsEnabled(true);
            $obj->setChapter(null);
            $obj->setRole($data['role']);
            $obj->setOriginalSizeKB("0");
            $obj->setConvertedSizeKB("0");
            $obj->setStatus('new');

            $this->_em->persist($obj);
            if (! $this->isAllowed($obj, __FUNCTION__)) {
                $this->_em->remove($obj);
                throw new \exception(self::PERMISSION_DENIED);
            }

            $this->_em->flush();
            $form->id->setValue($obj->getId());

            $oldname =\pathinfo($form->file->getFileName());
            $obj->setOriginalExtension($oldname['extension']);

            $newname = SHARE_PATH . DIRECTORY_SEPARATOR . 'audio' .
                     DIRECTORY_SEPARATOR . $obj->getId() . '.' .
                     $oldname['extension'];

            //
            $form->file->addFilter('Rename',
                    array(
                            'target' => $newname,
                            'overwrite' => true
                    ));

            $form->getValues();

            $form->file->getTransferAdapter()->setOptions(
                    array(
                            'useByteString' => false
                    ));
            $this->_message('create_success');

            $obj->setOriginalSizeKB($form->file->getFileSize() / 1024);
            $this->_em->persist($obj);
            $this->_em->flush();
            try {

                $cl = \Zend_Registry::getInstance()->get('queue');
                $jobParams = array(
                        'environment' => APPLICATION_ENV,
                        'id' => $obj->getId(),
                        'server' => \Zend_Registry::getInstance()->get('system')->PRIVATE_IP
                );
                $cl->backgroundTask('DEP_Audio_SendOriginalToCloud', $jobParams);
            } catch (\exception $e) {
                $logger = \Zend_Registry::get('logger');
                $logger->log(
                        "There was a problem creating the processing task to handle the
              post-upload activities." . $e->getMessage(),
                        \Zend_Log::INFO);
                return false;
            }

            $this->_em->persist($obj);
            $this->_em->flush();
            return $obj->getId();
        } else {
            throw new \exception(self::FORM_INVALID);
        }
    }

    public function acl_update ($id, array $data)
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {
            if (! $this->isAllowed($obj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::NOT_FOUND);
        }

        $logger = \Zend_Registry::get('logger');
        $logger->log('Service/Content/Audio::update - ' . print_r($data, true),
                \Zend_Log::INFO);
        $form = $this->getForm();

        if ($form->getElement('course') != null) {
            $form->removeElement('course');
        }

        if ($form->isValid($data)) {
            $obj->setTitle($data['title']);
            $obj->setChapter(null);
            $obj->setDescription($data['description']);
            $obj->setIsPublished($data['isPublished']);
            $obj->setRole($data['role']);
            $this->_em->persist($obj);
            $this->_em->flush();
            $this->_message('update_success');
            $this->_em->persist($obj);
            $this->_em->flush();
            $this->_message('update_success');
            // unset($this->_form);
            return true;
        } else {
            $logger->log('Service/Content/Audio::update - ' . $form,

                    \Zend_Log::INFO);
            throw new \exception(self::FORM_INVALID);
            return false;
        }
    }

    public function acl_delete ($id)
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {
            if (! $this->isAllowed($obj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::NOT_FOUND);
        }

        $logger = \Zend_Registry::get('logger');

        if (! is_object($obj->getCourse())) {
            throw new \exception(self::COURSE_NOT_FOUND);
        }

        $this->system_deleteLocalData($obj->getId());

        if ($this->system_deleteCloudData(
                $obj->getId() . "." . $obj->getOriginalExtension()) == true) {} else {
            $logger->log(
                    "Service/Content/Audio/deleteOriginalFromCloud(\"" .
                             $obj->getId() . "\") failed.");
        }
        $this->_em->remove($obj);
        $this->_em->flush();
        $this->_message('delete_success');
        return true;
    }

    public function getForm ()
    {
        if (! isset($this->_form)) {
            $this->_form = new \App\Form\Content\Audio();
        }
        return $this->_form;
    }

    public function getDeleteForm ()
    {
        if (! isset($this->_deleteForm)) {
            $this->_deleteForm = new \App\Form\Content\AudioDelete();
        }
        return $this->_deleteForm;
    }

    public function system_deleteLocalData ($id)
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {} else {
            throw new \exception(self::NOT_FOUND);
        }
        if (file_exists(
                SHARE_PATH . "/audio/$id" . "." . $obj->getOriginalExtension())) {
            unlink(
                    realpath(
                            SHARE_PATH . "/audio/$id" . "." .
                                     $obj->getOriginalExtension()));
            return true;
        } else {
            return false;
        }
    }

    public function system_deleteCloudData ($id)
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {} else {
            throw new \exception(self::NOT_FOUND);
        }
        $id = $obj->getId();
        $cloud = new \App\Service\Core\Cloud();
        $originalFilename = $obj->getId() . "." . $obj->getOriginalExtension();
        try {
            $cloud->deleteFile("audio/converted/$id.mp4");
            $cloud->deleteFile("audio/original/$originalFilename");
        } catch (\exception $e) {}
        return true;
    }

    public function system_processData ($id, array $options)
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {} else {
            throw new \exception(self::NOT_FOUND);
        }

        $out = array(
                'responseCode' => null,
                'encoder' => NULL
        );

        $zencoder = new \Services_Zencoder(
                \Zend_Registry::getInstance()->get('encoder')->zencoder->API_KEY);
        $encoding_job = $zencoder->jobs->create(
                array(
                        "input" => "s3://" .
                                 \Zend_Registry::getInstance()->get('cloud')->aws->BUCKET .
                                 "/audio/original/" . $obj->getId() . "." .
                                 $obj->getOriginalExtension(),
                                "outputs" => array(
                                        array(
                                                "label" => "audio",
                                                "notifications" => array(
                                                        "http://" .
                                                         \Zend_Registry::getInstance()->get(
                                                                'system')->PUBLIC_SERVER_NAME .
                                                         "/Notification/Zencoder"
                                                ),
                                                "access_control" => array(
                                                        array(
                                                                "grantee" => \Zend_Registry::getInstance()->get(
                                                                        'cloud')->aws->CLOUDFRONT_CANONICAL_ID,
                                                                "permissions" => "READ"
                                                        ),
                                                        array(
                                                                "grantee" => \Zend_Registry::getInstance()->get(
                                                                        'cloud')->aws->CANONICAL_ID,
                                                                "permissions" => "FULL_CONTROL"
                                                        ),
                                                        array(
                                                                "grantee" => \Zend_Registry::getInstance()->get(
                                                                        'encoder')->zencoder->CANONICAL_ID,
                                                                "permissions" => array(
                                                                        "READ",
                                                                        "WRITE"
                                                                )
                                                        )
                                                ),
                                                "url" => "s3://" .
                                                 \Zend_Registry::getInstance()->get(
                                                        'cloud')->aws->BUCKET .
                                                 "/audio/converted/$id.mp4"
                                        )
                                )
                ));

        $encoder = new \App\Entity\Queue\Encoder();
        $encoderService = new \App\Service\Queue\Encoder();
        if ($encoding_job->id) {
            $encoder->setObjId($id);
            $encoder->setObjType('audio');
            $encoder->setJobId($encoding_job->id);
            $encoder->setStatus("transcoding");
            $encoder->setJobType("audio_full");
            $encoder->setPercentComplete(0);
            $this->_em->persist($encoder);
            $obj->setStatus("transcoding");
            $this->_em->flush();
            $out['responseCode'] = "transcoding";
            $out['encoder'] = $encoder;
        } else {
            $encoder->setErrorMessage($encoding_job->errors);
            $obj->setStatus("error");
            $this->_em->flush();
            $encoder->setErrorMessage(
                    "Transcoding Failed - $encoder->getErrorMessage()");
            $encoderService->update($encoder->toArray());
            $out['responseCode'] = "error";
            $out['encoder'] = $encoder;
        }
        return $out;
    }

    public function system_updateStatus ($id, $status, $convertedSizeKB, $durationInSeconds,
            $errorMessage = null)
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {} else {
            throw new \exception(self::NOT_FOUND);
        }
        $obj->setConvertedSizeKB($convertedSizeKB);
        $obj->setDurationInSeconds($durationInSeconds);
        $obj->setStatus($status);
        $obj->setErrorMessage($errorMessage);
        $this->_em->persist($obj);
        $this->_em->flush();
        return $obj;
    }

    public function system_sendDataToCloud ($id, $options = array())
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {} else {
            throw new \exception(self::NOT_FOUND);
        }
        if (! array_key_exists('server', $options)) {
            throw new \exception(self::SERVER_NOT_DEFINED);
        }
        if ($obj->getOriginalExtension() != NULL) {} else {
            $obj->setStatus("error");
            $obj->SetErrorMessage("File extension is null");
            $this->_em->persist($obj);
            $this->_em->flush();
            throw new \exception($obj->getErrorMessage() . "\n");
        }
        $destFolder = "audio/original/";
        $file = "/audio/" . $obj->getId() . "." . $obj->getOriginalExtension();
        if (file_exists(SHARE_PATH . $file) == TRUE) {} else {
            $obj->setStatus("error");
            $obj->setErrorMessage("$file doesn't exist locally.");
            $this->_em->persist($obj);
            $this->_em->flush();
            throw new \exception($obj->getErrorMessage() . "\n");
        }
        $cloud = new \App\Service\Core\Cloud();
        $obj->setStatus("uploading");
        $this->_em->persist($obj);
        $this->_em->flush();
        if ($cloud->uploadFile(SHARE_PATH . $file,
                $destFolder . $obj->getId() . "." . $obj->getOriginalExtension()) ==
                 true) {
            $obj->setStatus("uploaded");
            $this->_em->persist($obj);
            $this->_em->flush();
            $cloud->setACL(
                    "audio/original/" . $obj->getId() . "." .
                             $obj->getOriginalExtension());
            return true;
        } else {
            $obj->setStatus("error");
            $obj->setErrorMessage(
                    "There was a problem sending the file to the cloud.");
            $this->_em->persist($obj);
            $this->_em->flush();
        }
    }

    public function getDistributionStreamer ()
    {
        return \Zend_Registry::getInstance()->get('cloud')->aws->CLOUDFRONT_DISTRIBUTION_STREAMER;
    }

    public function acl_getStreamUrl ($id, $options = array())
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {
            if (! $this->isAllowed($obj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::NOT_FOUND);
        }

        if (! isset($options['minutes'])) {
            $options['minutes'] = null;
        }
        if (! isset($options['IPAddress'])) {
            $options['IPAddress'] = null;
        }
        if (null !== $options['minutes']) {
            $numberOfMinutes = (int) $options['minutes'];
        } else {
            $numberOfMinutes = 30;
        }
        if (null !== $options['IPAddress']) {
            $IPAddress = mysql_real_escape_string($options['IPAddress']);
        } else {
            $IPAddress = null;
        }
        $filename = "audio/converted/" . $obj->getId() . ".mp4";
        $cf = new \AmazonCloudFront();
        $distribution_hostname = "vme-stream.thesisplanet.com";
        // Options should include restrictors such as IP Address, Duration, etc.
        $expires = strtotime('+' . $numberOfMinutes . 'minutes'); // time
        $url = $cf->get_private_object_url($distribution_hostname, $filename,
                $expires, $opt = null);
        $cfn = new \cloudFrontNinja();
        $signedFile =         // urlencode(
        $cfn->get_private_object_path($filename, $expires, $opt = null); // )
        return $signedFile;
    }

    public function acl_getDownloadUrl ($id, $options = array())
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {
            if (! $this->isAllowed($obj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::NOT_FOUND);
        }

        $opt = array();
        $opt['Secure'] = false;
        if (! isset($options['minutes'])) {
            $options['minutes'] = null;
            $numberOfMinutes = 30;
        } else {
            $numberOfMinutes = (int) $options['minutes'];
        }
        if (isset($options['IPAddress'])) {
            $opt['IPAddress'] = $options['IPAddress'];
        }
        $filename = "audio/converted/" . $obj->getId() . ".mp4";
        $cf = new \AmazonCloudFront();
        $distribution_hostname = \Zend_Registry::getInstance()->get('cloud')->aws->CLOUDFRONT_DOWNLOAD_DISTRIBUTION_URL;
        // Options should include restrictors such as IP Address, Duration, etc.
        $expires = strtotime('+' . $numberOfMinutes . 'minutes'); // time
        $url = $cf->get_private_object_url($distribution_hostname, $filename,
                $expires, $opt);
        return $url;
    }
}