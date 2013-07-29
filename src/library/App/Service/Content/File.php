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

class File extends \App\Service\aContent implements \App\Service\Content\iFile
{

    protected $_repository = null;

    protected $_courseRepository = null;

    protected $_form = null;

    protected $_deleteForm = null;

    protected $_em = null;

    const NOT_FOUND = "Object not found.";

    const FORM_INVALID = "Invalid form entry";

    public function __construct ($options = array())
    {
        $this->_em = \Zend_Registry::get('em');
        $this->_repository = $this->_em->getRepository(
                '\App\Entity\Content\File');
        $this->_courseRepository = $this->_em->getRepository(
                '\App\Entity\Course');
        $this->_acl = new \App\Service\ACL\Content\File();
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

    public function getForm ()
    {
        if (! isset($this->_form)) {
            $this->_form = new \App\Form\Content\File();
        }
        return $this->_form;
    }

    public function getDeleteForm ()
    {
        if (null === $this->_form) {
            $this->_deleteForm = new \App\Form\Content\FileDelete();
        }
        return $this->_deleteForm;
    }

    public function acl_create (array $data)
    {
        $form = $this->getForm();
        $form->setSubmitLabel("Create");
        if ($form->isValid($data)) {
            $obj = new \App\Entity\Content\File();
            $obj->setTitle($data['title']);
            $obj->setDescription($data['description']);
            $obj->setCourse($this->_courseRepository->find($data['course']));
            $obj->setIsPublished($data['isPublished']);
            $obj->setIsEnabled(true);
            $obj->setRole($data['role']);
            $obj->setOriginalSizeKB("0");
            $obj->setStatus('new');
            $obj->setChapter(null);
            $this->_em->persist($obj);
            if (! $this->isAllowed($obj, __FUNCTION__)) {
                $this->_em->remove($obj);
                throw new \exception(self::PERMISSION_DENIED);
            }
            
            $this->_em->flush();
            $form->id->setValue($obj->getId());
            
            $oldname = \pathinfo($form->file->getFileName());
            $obj->setOriginalExtension($oldname['extension']);
            
            $newname = SHARE_PATH . DIRECTORY_SEPARATOR . 'file' .
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
                $cl->backgroundTask('DEP_File_SendOriginalToCloud', $jobParams);
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
                    "Service/Content/File/deleteOriginalFromCloud(\"" .
                             $obj->getId() . "\") failed.");
        }
        $this->_em->remove($obj);
        $this->_em->flush();
        $this->_message('delete_success');
        return true;
    }

    public function system_deleteLocalData ($id)
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {} else {
            throw new \exception(self::NOT_FOUND);
        }
        if (file_exists(
                SHARE_PATH . "/file/$id" . "." . $obj->getOriginalExtension())) {
            unlink(
                    realpath(
                            SHARE_PATH . "/file/$id" . "." .
                                     $obj->getOriginalExtension()));
            return true;
        } else {
            return false;
        }
    }

    public function system_deleteCloudData ($id)
    {
        $obj = $this->find($id);
        if (! is_object($obj)) {
            throw new \exception(self::NOT_FOUND);
        }
        $cloud = new \App\Service\Core\Cloud();
        if ($cloud->deleteFile(
                "file/original/" . $obj->getId() . "." .
                         $obj->getOriginalExtension())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This function doesn't do anything because no processing needs to take
     * place at present for files.
     *
     * @see \App\Service\iContent::system_processData()
     */
    public function system_processData ($id, array $options)
    {
        $obj = $this->find($id);
        if (! is_object($obj)) {
            throw new \exception(self::NOT_FOUND);
        }
        
        return true;
    }

    public function system_sendDataToCloud ($id, $options = array())
    {
        $obj = $this->find($id);
        if (! is_object($obj)) {
            throw new \exception(self::NOT_FOUND);
        }
        if (! array_key_exists('server', $options)) {
            throw new \exception(
                    'Please provide the server that is has the original copy.');
        }
        if ($obj->getOriginalExtension() != NULL) {} else {
            $obj->setStatus("error");
            $obj->SetErrorMessage("File extension is null");
            $this->_em->persist($obj);
            $this->_em->flush();
            throw new \exception($obj->getErrorMessage() . "\n");
            return FALSE;
        }
        $destFolder = "file/original/";
        $file = "/file/" . $obj->getId() . "." . $obj->getOriginalExtension();
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
            $obj->setStatus("ready");
            $this->_em->persist($obj);
            $this->_em->flush();
            $cloud->setACL(
                    "file/original/" . $obj->getId() . "." .
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
        $url = "/images/icons/filetypes/" . $obj->getOriginalExtension() . ".png";
        return $url;
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
        $filename = "file/original/" . $obj->getId() . "." .
                 $obj->getOriginalExtension();
        $cf = new \AmazonCloudFront();
        $distribution_hostname = \Zend_Registry::getInstance()->get('cloud')->aws->CLOUDFRONT_DOWNLOAD_DISTRIBUTION_URL;
        // Options should include restrictors such as IP Address, Duration, etc.
        $expires = strtotime('+' . $numberOfMinutes . 'minutes'); // time
        $url = $cf->get_private_object_url($distribution_hostname, $filename, 
                $expires, $opt);
        return $url;
    }
}