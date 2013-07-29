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

class Video extends \App\Service\aContent implements \App\Service\Content\iVideo
{

    protected $_repository = null;

    protected $_channelRepository = null;

    protected $_form = null;

    protected $_deleteForm = null;

    protected $_em = null;

    protected $_acl = null;

    const NOT_FOUND = "That video content was not found.";

    public function __construct ($options = array())
    {
        $this->_em = \Zend_Registry::get('em');
        $this->_repository = $this->_em->getRepository(
                '\App\Entity\Content\Video');
        $this->_courseRepository = $this->_em->getRepository(
                '\App\Entity\Course');
        $this->_acl = new \App\Service\ACL\Content\Video();
    }

    /**
     *
     * @return object /App/Entity/Content/Video
     * @throws \exception
     */
    public function find ($id)
    {
        return $this->_repository->find($id);
    }

    public function acl_create (array $data)
    {
        $form = $this->getForm();
        $form->setSubmitLabel("Create");
        if ($form->isValid($data)) {
            $obj = new \App\Entity\Content\Video();
            $obj->setTitle($data['title']);
            $obj->setDescription($data['description']);
            $obj->setCourse($this->_courseRepository->find($data['course']));
            $obj->setIsPublished($data['isPublished']);
            $obj->setIsEnabled(true);
            $obj->setRole($data['role']);
            $obj->setOriginalSizeKB("0");
            $obj->setConvertedSizeKB("0");
            $obj->setStatus('new');
            $obj->setChapter(null);
            $this->_em->persist($obj);
            if (! $this->isAllowed($obj, __FUNCTION__)) {
                $this->_em->remove($obj);
                throw new \exception(self::PERMISSION_DENIED);
            }

            $this->_em->flush();
            $form->id->setValue($obj->getId());

            $oldname =\pathinfo($form->file->getFileName());
            $obj->setOriginalExtension($oldname['extension']);

            $newname = SHARE_PATH . DIRECTORY_SEPARATOR . 'video' .
                     DIRECTORY_SEPARATOR . $obj->getId() . '.' .
                     $oldname['extension'];

            $form->file->addFilter('Rename',
                    array(
                            'target' => $newname,
                            'overwrite' => true
                    ));
            // $form->file->receive();
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
                $cl->backgroundTask('DEP_Video_SendOriginalToCloud', $jobParams);
            } catch (\exception $e) {
                throw new \exception(
                        "There was a problem creating the processing task to handle the
              post-upload activities." . $e->getMessage());
            }
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
        $form = $this->getForm();

        if ($form->getElement('course') != null) {
            $form->removeElement('course');
        }
        if ($form->isValid($data)) {
            $obj->setTitle($data['title']);
            $obj->setDescription($data['description']);
            $obj->setIsPublished($data['isPublished']);
            $obj->setRole($data['role']);
            $obj->setChapter(null);
            $this->_em->persist($obj);
            $this->_em->flush();
            $this->_message('update_success');
            return true;
        } else {
            throw new \exception(self::FORM_INVALID);
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

        if ($this->system_deleteCloudData($obj->getId())) {} else {
            $logger->log(
                    "Service/Content/Video/deleteOriginalFromCloud(\"" .
                             $obj->getId() . "\") failed.");
        }
        $this->_em->remove($obj);
        $this->_em->flush();
        $this->_message('delete_success');
        return true;
    }

    public function getForm ()
    {
        if (null === $this->_form) {
            $this->_form = new \App\Form\Content\Video();
        }
        return $this->_form;
    }

    public function getDeleteForm ()
    {
        if (null === $this->_deleteForm) {
            $this->_deleteForm = new \App\Form\Content\VideoDelete();
        }
        return $this->_deleteForm;
    }

    public function getDistributionStreamer ()
    {
        return \Zend_Registry::getInstance()->get('cloud')->aws->CLOUDFRONT_DISTRIBUTION_STREAMER;
    }

    /*
     * (non-PHPdoc) @see \App\Service\iContent::findByCourseId()
     */
    public function findByCourseId ($courseId)
    {
        return $this->_repository->findBy(
                array(
                        '_course' => $courseId
                ));
    }

    /*
     * (non-PHPdoc) @see \App\Service\iContent::system_deleteLocalData()
     */
    public function system_deleteLocalData ($id)
    {
        $obj = $this->find($id);
        if (is_object($obj)) {} else {
            throw new \exception(self::NOT_FOUND);
        }
        if (file_exists(
                SHARE_PATH . "/video/$id" . "." . $obj->getOriginalExtension())) {
            unlink(
                    realpath(
                            SHARE_PATH . "/video/$id" . "." .
                                     $obj->getOriginalExtension()));
            return true;
        } else {
            return false;
        }
    }

    /*
     * (non-PHPdoc) @see \App\Service\iContent::system_deleteCloudData()
     */
    public function system_deleteCloudData ($id)
    {
        $obj = $this->find($id);
        if (! is_object($obj)) {
            throw new \exception(self::NOT_FOUND);
        }
        $cloud = new \App\Service\Core\Cloud();
        $cloud->deleteFile(
                "video/original/" . $obj->getId() . "." .
                         $obj->getOriginalExtension());
        $cloud->deleteFile("video/converted/" . $obj->getId() . ".mp4");
        return true;
    }

    /*
     * (non-PHPdoc) @see \App\Service\iContent::system_processData()
     */
    public function system_processData ($id, array $options)
    {
        $obj = $this->find($id);
        if (! is_object($obj)) {
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
                        "api_key" => \Zend_Registry::getInstance()->get(
                                'encoder')->zencoder->API_KEY,
                        "input" => "s3://" .
                                 \Zend_Registry::getInstance()->get('cloud')->aws->BUCKET .
                                 "/video/original/" . $obj->getId() . "." .
                                 $obj->getOriginalExtension(),
                                "outputs" => array(
                                        array(
                                                "label" => "web",
                                                "url" => "s3://" .
                                                 \Zend_Registry::getInstance()->get(
                                                        'cloud')->aws->BUCKET .
                                                 "/video/converted/$id.mp4",
                                                "width" => "640",
                                                "height" => "480",
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
                                                "thumbnails" => array(
                                                        array(
                                                                "label" => "small_thumbnail",
                                                                "base_url" => "s3://" .
                                                                 \Zend_Registry::getInstance()->get(
                                                                        'cloud')->aws->BUCKET .
                                                                 "/video/thumbnails/small",
                                                                "filename" => "$id",
                                                                "format" => "png",
                                                                "width" => "125",
                                                                "height" => "94",
                                                                "aspect_mode" => "pad",
                                                                "number" => 1,
                                                                "offset" => "120"
                                                        ), // End
                                                           // of
                                                           // thumbnail
                                                           // 1
                                                        array(
                                                                "label" => "large_thumbnail",
                                                                "base_url" => "s3://" .
                                                                 \Zend_Registry::getInstance()->get(
                                                                        'cloud')->aws->BUCKET .
                                                                 "/video/thumbnails/large",
                                                                "filename" => "$id",
                                                                "format" => "png",
                                                                "width" => "480",
                                                                "height" => "270",
                                                                "aspect_mode" => "pad",
                                                                "number" => 1,
                                                                "offset" => "120"
                                                        )
                                                )
                                        )
                                )
                )); // End of thumbnail 2
        $encoder = new \App\Entity\Queue\Encoder();
        $encoderService = new \App\Service\Queue\Encoder();
        if ($encoding_job->id) {
            $encoder->setObjId($id);
            $encoder->setObjType('video');
            $encoder->setJobId($encoding_job->id);
            $encoder->setStatus("transcoding");
            $encoder->setJobType("video_full");
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

    /*
     * (non-PHPdoc) @see \App\Service\iContent::system_sendDataToCloud()
     */
    public function system_sendDataToCloud ($id, $options = array())
    {
        if (! array_key_exists('server', $options)) {
            throw new \exception(
                    'Please provide the server that is has the original copy.');
        }
        $obj = $this->find($id);
        if (! is_object($obj)) {
            throw new \exception(self::NOT_FOUND);
        }
        if ($obj->getOriginalExtension() != NULL) {} else {
            $obj->setStatus("error");
            $obj->SetErrorMessage("File extension is null");
            $this->_em->persist($obj);
            $this->_em->flush();
            throw new \exception($obj->getErrorMessage() . "\n");
            return FALSE;
        }
        $destFolder = "video/original/";
        $file = "/video/" . $obj->getId() . "." . $obj->getOriginalExtension();
        if (file_exists(SHARE_PATH . $file) == TRUE) {} else {
            $obj->setStatus("error");
            $obj->setErrorMessage("$file doesn't exist locally.");
            $this->_em->persist($obj);
            $this->_em->flush();
            throw new \exception($obj->getErrorMessage() . "\n");
            return FALSE;
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
                    "video/original/" . $obj->getId() . "." .
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

    public function system_updateStatus ($id, $status, $convertedSizeKB,
            $durationInSeconds, $errorMessage = null)
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
        if (! isset($options['IPAddress'])) {} else {
            $opt['IPAddress'] = $options['IPAddress'];
        }

        $filename = "video/converted/" . $obj->getId() . ".mp4";
        $cf = new \AmazonCloudFront();
        $distribution_hostname = \Zend_Registry::getInstance()->get('cloud')->aws->CLOUDFRONT_DOWNLOAD_DISTRIBUTION_URL;
        // Options should include restrictors such as IP Address, Duration, etc.
        $expires = strtotime('+' . $numberOfMinutes . 'minutes'); // time
        $url = $cf->get_private_object_url($distribution_hostname, $filename,
                $expires, $opt);
        return $url;
    }

    public function acl_getThumbnailUrl ($id, $options = array())
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
        if (! isset($options['IPAddress'])) {} else {
            $opt['IPAddress'] = $options['IPAddress'];
        }

        if (array_key_exists('size', $options)) {
            $size = $options['size'];
        } else {
            $size = 'small';
        }

        $filename = "video/thumbnails/$size/" . $obj->getId() . ".png";
        $cf = new \AmazonCloudFront();
        $distribution_hostname = \Zend_Registry::getInstance()->get('cloud')->aws->CLOUDFRONT_DOWNLOAD_DISTRIBUTION_URL;
        // Options should include restrictors such as IP Address, Duration, etc.
        $expires = strtotime('+' . $numberOfMinutes . 'minutes'); // time
        $url = $cf->get_private_object_url($distribution_hostname, $filename,
                $expires, $opt);
        return $url;
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
        $filename = "video/converted/" . $obj->getId() . ".mp4";
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
}