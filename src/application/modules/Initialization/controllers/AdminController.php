<?php

class Initialization_AdminController extends Zend_Controller_Action
{

    public function init ()
    {}

    public function indexAction ()
    {
        $this->view->page = "Register an Admin account";
        $userService = new \App\Service\User();
        $form = $userService->getForm();
        $form->setSubmitLabel("Create my account!");
        $form->getElement('submit')->setOptions(
                array(
                        'class' => 'btn btn-primary btn-large center'
                ));
        $form->removeElement('role');
        $form->removeElement('activated');
        if ($this->_request->isPost()) {
            $form->getElement('username')->addValidator(
                    new \App\Validate\User\Username());
            $form->getElement('email')->addValidator(
                    new \App\Validate\User\Email());
            if ($form->isValid($this->_request->getParams())) {
                $userId = $userService->register($this->_request->getParams());
                $userService->sendRegistrationEmail($userId);
                $this->_redirect("/registersuccess");



                $userService->acl_admin_update($id, $data);
            }
        }
        $this->view->form = $form;
    }
}

