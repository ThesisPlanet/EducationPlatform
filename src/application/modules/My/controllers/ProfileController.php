<?php

class My_ProfileController extends Zend_Controller_Action
{

    public function init ()
    {
        $this->_service = new \App\Service\User();
        if (\Zend_Auth::getInstance()->hasIdentity()) {
            $this->_service->setUser(
                    \Zend_Auth::getInstance()->getIdentity()
                        ->getUser());
        }
        if (\Zend_Auth::getInstance()->hasIdentity()) {
            $this->_service->setUser(
                    \Zend_Auth::getInstance()->getIdentity()
                        ->getUser());
        }
        $this->view->service = $this->_service;
    }

    public function indexAction ()
    {
        $user = \Zend_Auth::getInstance()->getIdentity()->getUser();

        try {
            $userProfile = $this->_service->acl_getProfile($user->getId());
            $this->view->pic_url = $userProfile['pic_url'];
        } catch (\exception $e) {
            $this->view->pic_url = "";
        }

        $form = new \App\Form\User();
        $form->populate($user->toArray());

        $this->view->page = "My Profile";

        $form->removeElement('username');
        $form->removeElement('activated');
        $form->removeElement('role');
        $form->removeElement('password');
        $form->removeElement('email');
        $form->removeElement('submit');

        // Image file

        $file = new \Zend_Form_Element_File('file');
        $file->setLabel('File')
            ->setRequired(false)
            ->setMaxFileSize('209715200')
            ->addValidator('MimeType', false,
                array(
                        'image/png',
                        'image/jpeg',
                        'application/octet-stream'
                ));

        $form->addElement($file);

        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Update profile')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));

        $form->addElement($submit);
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {

                $result = $this->_service->acl_updateProfile(
                        $this->_request->getParams());
            }
        }

        $this->view->form = $form;
    }
}

