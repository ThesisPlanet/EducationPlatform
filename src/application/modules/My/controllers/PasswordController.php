<?php
class My_PasswordController extends Zend_Controller_Action
{
    public function init ()
    {
        /*
         * Initialize action controller here
         */
    }
    public function indexAction ()
    {
        $service = new \App\Service\User();
        $form = $service->getResetPasswordForm();
        if (\Zend_Auth::getInstance()->hasIdentity()) {
            $user = \Zend_Auth::getInstance()->getIdentity()->getUser();
            $correctToken = hash('sha512',
            $user->getId() . $user->getEmail() . $user->getUsername() .
             $user->getPassword());
            $form = new \App\Form\User\ResetPassword();
            $token = new \Zend_Form_Element_Hidden('token');
            $token->setValue($correctToken);
            $form->addElement($token);
        }
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {}
            $data = array(
            'email' => \Zend_Auth::getInstance()->getIdentity()
                ->getUser()
                ->getEmail(), 'token' => $form->getValue('token'),
            'password1' => $form->getValue('password1'),
            'password2' => $form->getvalue('password2'));
            if ($service->resetPassword($data)) {
                $this->_helper->flashMessenger($service->getMessage());
                $this->_redirect('/My/password');
            }
        }
        $this->view->form = $form;
        $user = $service->find(
        \Zend_Auth::getInstance()->getIdentity()
            ->getUser()
            ->getId());
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
}

