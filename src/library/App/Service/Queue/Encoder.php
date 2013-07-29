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
namespace App\Service\Queue;

class Encoder extends \App\Service\Base
{

    protected $_form;

    protected $_repository = null;

    protected $_em = null;

    public function __construct ()
    {
        $this->_em = \Zend_Registry::get('em');
        $this->_repository = $this->_em->getRepository(
                '\App\Entity\Queue\Encoder');
    }

    public function find ($id)
    {
        return $this->_repository->find($id);
    }

    public function findByJobId ($jobId)
    {
        return $this->_repository->findBy(
                array(
                        'jobId' => $jobId
                ));
    }

    public function findAll ()
    {
        return $this->_repository->findAll();
    }

    public function create ($data = array())
    {
        $form = $this->getForm();
        $form->setSubmitLabel("Create");
        if ($form->isValid($data)) {
            $obj = new \App\Entity\Queue\Encoder();
            $obj->setObjId($data['objId']);
            $obj->setObjType($data['objType']);
            $obj->setStatus($data['status']);
            $obj->setJobId($data['jobId']);
            $obj->setPercentComplete($data['percentComplete']);
            $obj->setErrorMessage($data['errorMessage']);
            $obj->setJobType($data['jobType']);
            $obj->setDurationSeconds($data['durationSeconds']);
            try {
                $this->_em->persist($obj);
                $this->_em->flush();
                $this->_message('create_success');
                return $obj;
            } catch (\exception $e) {
                echo "Unable to create. " . $e->getMessage();
                return false;
            }
        } else {
            throw new \exception("Inputs are not valid.");
            return $obj;
        }
    }

    public function update ($data = array())
    {
        $logger = \Zend_Registry::getInstance()->get('logger');
        $form = $this->getForm();
        $this->_form->setSubmitLabel("Update");
        if ($form->isValid($data)) {
            $obj = $this->_repository->find($data['id']);
            $obj->setObjId($data['objId']);
            $obj->setObjType($data['objType']);
            $obj->setStatus($data['status']);
            $obj->setJobId($data['jobId']);
            $obj->setPercentComplete($data['percentComplete']);
            $obj->setErrorMessage($data['errorMessage']);
            $obj->setJobType($data['jobType']);
            $obj->setDurationSeconds($data['durationSeconds']);
            try {
                $this->_em->persist($obj);
                $this->_em->flush();
                $this->_message('update_success');
                return $obj;
            } catch (\exception $e) {
                echo "Unable to update. " . $e->getMessage();
                return false;
            }
        } else {
            $logger->log("Form not valid - " . $this->getForm(), \Zend_Log::ERR);
        }
    }

    public function delete ($data = array())
    {
        $form = $this->getDeleteForm();
        if ($form->isValid($data)) {
            $data['id'] = (int) $data['id'];
            if ($this->_repository->remove($data['id'])) {
                $this->_em->flush();
                $this->_message('delete_success');
                return true;
            }
        }
    }

    public function getForm ($id = null)
    {
        if (null === $this->_form) {
            if (null === $id) {
                $this->_form = new \App\Form\Queue\Encoder();
            } else {
                $obj = $$this->_repository->find($id);
                $this->_form = new \App\Form\Queue\Encoder();
                $this->_form->populate($obj->toArray());
            }
        }
        return $this->_form;
    }

    public function getDeleteForm ($id = null)
    {
        if (null === $this->_form) {
            if (null === $id) {
                $this->_form = new \App\Form\Queue\EncoderDelete();
            } else {
                $obj = $this->_defaultMapper->fetch($id);
                $this->_form = new \App\Form\Queue\EncoderDelete();
                $this->_form->populate($obj->toArray());
            }
        }
        return $this->_form;
    }
    // External actions
    public function fetchDuration ($jobId)
    {
        $zencoder = new \Services_Zencoder(
                \Zend_Registry::getInstance()->get('encoder')->zencoder->API_KEY);

        $details = $zencoder->jobs->details($jobId);
        $EncodingDuration = 0;
        if ($details) {

                $duration = $details->input->duration_in_ms / 1000;
                $EncodingDuration += (float) $duration;

        } else {
            return false;
        }
        return $EncodingDuration;
    }

    public function fetchOutputSizeInKb ($jobId)
    {
        $zencoder = new \Services_Zencoder(
                \Zend_Registry::getInstance()->get('encoder')->zencoder->API_KEY);

        $details = $zencoder->jobs->details($jobId);

        $totalSize = 0;

        foreach ($details->outputs as $output) {
            $sizeInKb = $output->file_size_bytes / 1024;
            $totalSize += (float) $sizeInKb;
        }
        return $totalSize;
    }

    public function fetchUnallocated ()
    {
        return $this->_repository->findUnallocated();
    }
}