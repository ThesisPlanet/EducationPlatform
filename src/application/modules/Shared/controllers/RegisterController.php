<?php

class Shared_RegisterController extends Zend_Controller_Action
{

    public function init ()
    {
        /*
         * Initialize action controller here
         */
    }

    public function indexAction ()
    {
        $users = \Zend_Registry::getInstance()->get('users');
        if (isset($users->canRegister)) {
            if ($users->canRegister == true) {
                $this->view->displayRegister = true;
            } else {
                $this->_redirect("/login");
            }
        } else {
            $this->_redirect("/login");
        }

        $this->view->page = "Register a new account";
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
            }
        }
        $this->view->form = $form;
    }

    public function successAction ()
    {}
}

