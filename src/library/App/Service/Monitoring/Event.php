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
namespace App\Service\Monitoring;
class Event extends \App\Service\Base
{
    protected $_form;
    protected $_deleteForm;
    protected $_em = null;
    protected $_repository = null;
    public function __construct ()
    {
        $this->_em = \Zend_Registry::get('em');
        $this->_repository = $this->_em->getRepository(
        '\App\Entity\Monitoring\Event');
    }
    public function find ($id)
    {
        $obj = $this->_repository->find($id);
        return $obj;
    }
    public function findAll ()
    {
        $List = $this->_repository->findAll();
        return $List;
    }
    public function create ($data = array())
    {
        $form = $this->getForm();
        $form->setSubmitLabel("Create");
        if ($form->isValid($data)) {
            $obj = new \App\Entity\Monitoring\Event();
            $obj->setTimestamp(new \DateTime("@" . time()));
            $obj->setMessage($form->getValue('message'));
            $obj->setDeviceIp($form->getValue('deviceIp'));
            $obj->setDeviceName($form->getValue('deviceName'));
            $obj->setPriority($form->getValue('priority'));
            $obj->setPriorityName($form->getValue('priorityName'));
            try {
                //throw new \exception(print_r($data, true));
                $this->_em->persist($obj);
                $this->_em->flush();
                $this->_message('create_success');
                return $obj;
            } catch (\exception $e) {
                echo "Unable to create. " . $e->getMessage();
                return false;
            }
        }
    }
    public function update ($data = array())
    {
        $form = $this->getForm();
        $this->_form->setSubmitLabel("Update");
        if ($form->isValid($data)) {
            $obj = $this->_repository->find($data['id']);
            $obj->setMessage($form->getValue('message'));
            $obj->setDeviceIp($form->getValue('deviceIp'));
            $obj->setDeviceName($form->getValue('deviceName'));
            $obj->setPriority($form->getValue('priority'));
            $obj->setPriorityName($form->getValue('priorityName'));
            try {
                $this->_em->persist($obj);
                $this->_em->flush();
                $this->_message('update_success');
                return $obj;
            } catch (\exception $e) {
                echo "Unable to update. " . $e->getMessage();
                return false;
            }
        }
    }
    public function delete ($data = array())
    {
        $form = $this->getDeleteForm();
        if ($form->isValid($data)) {
            $data['id'] = (int) $data['id'];
            $this->_repository->remove($data['id']);
            $this->_em->flush();
            $this->_message('delete_success');
            return true;
        }
    }
    public function getForm ($id = null)
    {
        if (null === $this->_form) {
            if (null === $id) {
                $this->_form = new \App\Form\Monitoring\Event();
            } else {
                $obj = $this->_defaultMapper->fetch($id);
                $this->_form = new \App\Form\Monitoring\Event();
                $this->_form->populate($obj->toArray());
            }
        }
        return $this->_form;
    }
    public function getDeleteForm ($id = null)
    {
        if (null === $this->_delteForm) {
            if (null === $id) {
                $this->_deleteForm = new \App\Form\Monitoring\EventDelete();
            } else {
                $obj = $this->_defaultMapper->fetch($id);
                $this->_deleteForm = new \App\Form\Monitoring\EventDelete();
                $this->_deleteForm->populate($obj->toArray());
            }
        }
        return $this->_deleteForm;
    }
    public function findRecentEvents ($page = null, $count = null)
    {
        $page = (int) $page;
        $count = (int) $count;
        if ($page == null) {
            $page = 1;
        }
        if ($count == null) {
            $count = 100;
        }
        return $this->_repository->fetchRecentEvents($page, $count);
    }
}