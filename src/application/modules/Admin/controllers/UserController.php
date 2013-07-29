<?php

class Admin_UserController extends Zend_Controller_Action
{

    protected $_service;

    public function preDispatch ()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $role = Zend_Auth::getInstance()->getIdentity()->getRoleId();
            if ($role != 'admin') {
                $this->_helper->redirector('/error/notAuthorized');
            }
        } else {
            $this->_redirect('/login');
        }
    }

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
        $this->view->page = "Users";
        $userList = $this->_service->findAll();
        $this->view->userList = $userList;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    public function editAction ()
    {
        $this->view->page = "Edit user";
        $id = $this->getRequest()->getParam('id');
        $this->view->id = $id;
        $obj = $this->_service->find($id);
        if (is_object($obj)) {
            
            $form = $this->_service->getForm($this->_request->getParam('id'));
            $form->populate($obj->toArray());
            $form->removeElement('username');
            $form->removeElement('password');
            // $form->removeElement('role');
            $form->removeElement('activated');
            if ($this->_request->isPost()) {
                if ($this->_service->acl_admin_update($id, 
                        $this->_request->getParams())) {
                    $this->_helper->flashMessenger(
                            $this->_service->getMessage());
                    $this->_helper->redirector('index');
                }
            }
        }
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->view->form = $form;
    }

    public function deleteAction ()
    {
        $this->view->page = "Delete user";
        $id = $this->getRequest()->getParam('id');
        $this->view->id = $id;
        $obj = $this->_service->find($id);
        $form = $this->_service->getDeleteForm();
        if (is_object($obj)) {
            $this->view->user = $obj;
            $form->populate($obj->toArray());
            if ($this->_request->isPost()) {
                if ($this->_service->acl_delete($id)) {
                    $this->_helper->flashMessenger(
                            $this->_service->getMessage());
                    $this->_helper->redirector('index');
                }
            }
        }
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->view->form = $form;
    }

    public function addAction ()
    {
        $this->view->page = "Add a new user";
        $form = $this->_service->getForm();
        $form->removeElement('role');
        $form->removeElement('activated');
        $form->getElement('username')->addValidator(
                new \App\Validate\User\Username());
        $form->getElement('email')->addValidator(new \App\Validate\User\Email());
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {
                $result = $this->_service->register($form->getValues());
                if ($result) {
                    $this->_service->sendRegistrationEmail($result);
                    $this->_helper->redirector('index');
                }
            }
        }
        $this->view->form = $form;
    }
}

