<?php

class Site_VideoController extends Zend_Controller_Action
{

    public function init ()
    {
        $this->_service = new \App\Service\Content\Video();
        $this->_courseService = new \App\Service\Course();
        if (\Zend_Auth::getInstance()->hasIdentity()) {
            $this->_service->setUser(
                    \Zend_Auth::getInstance()->getIdentity()
                        ->getUser());
            $this->_courseService->setUser(
                    \Zend_Auth::getInstance()->getIdentity()
                        ->getUser());
        }
        $this->view->service = $this->_service;
    }

    public function indexAction ()
    {
        // action body
    }

    public function viewAction ()
    {
        $obj = $this->_service->find(
                $this->getRequest()
                    ->getParam('id'));
        if (is_object($obj)) {
            if ($obj->getIsPublished() != 1) {
                throw new \exception("Sorry, that audio file is not published.");
            } else {
                if ($this->_service->dynamicACLIsAllowed($obj, "acl_view")) {
                    $this->view->video = $obj;
                } else {
                    throw new \exception(
                            "you do not have sufficient privileges to view this content.");
                }
            }
            
            $course = $obj->getCourse();
            if (\Zend_Auth::getInstance()->hasIdentity()) {
                $subscriptions = $this->_courseService->acl_findSubscriptions();
                if (is_array($subscriptions)) {
                    foreach ($subscriptions as $key => $subscriptionObj) {
                        if ($subscriptionObj->getCourse()->getId() ==
                                 $course->getId()) {
                            $completedContent = $subscriptionObj->getCompletedContent();
                            break;
                        }
                    }
                    if (is_array($completedContent))
                        foreach ($completedContent as $key => $value) {
                            if ($key == $obj->getId()) {
                                $isComplete = true;
                                break;
                            } else {
                                $isComplete = false;
                            }
                        }
                    if (! isset($isComplete)) {
                        $isComplete = false;
                    }
                    $this->view->isComplete = $isComplete;
                }
            }
        }
    }

    public function addAction ()
    {
        if (null !== $this->_request->getParam('id')) {
            $courseId = $this->_request->getParam('id');
        } else {
            $courseId = null;
        }
        $form = $this->_service->getForm(null);
        $form->removeElement('originalExtension');
        $form->removeElement('originalSizeKB');
        $form->removeElement('course');
        $form->removeElement('convertedSizeKB');
        
        if ($this->_request->isPost()) {
            
            $data = $this->_request->getParams();
            $data['course'] = $courseId;
            unset($data['id']);
            
            if ($form->isValid($data)) {
                $data['originalExtension'] = null;
                
                if ($this->_service->acl_create($data)) {
                    $this->_helper->flashMessenger(
                            $this->_service->getMessage());
                    $this->_redirect('/course/curriculum/' . $courseId);
                }
            }
        }
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->view->form = $form;
        $this->view->courseId = $courseId;
    }

    public function editAction ()
    {
        $obj = $this->_service->find($this->_request->getParam('id'));
        if (! $this->_service->isAllowed($obj, 'acl_update')) {
            throw new \exception("permission Denied.");
        }
        
        $form = $this->_service->getForm();
        $form->populate($obj->toArray());
        $form->removeElement('originalExtension');
        $form->removeElement('originalSizeKB');
        $form->removeElement('convertedSizeKB');
        $form->removeElement('course');
        $form->removeElement('file');
        if ($this->_request->isPost()) {
            $data = $this->_request->getParams();
            if ($this->_service->acl_update($obj->getId(), $data)) {
                $this->_helper->flashMessenger($this->_service->getMessage());
                $this->_redirect(
                        '/course/curriculum/' . $obj->getCourse()
                            ->getId());
            }
        }
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->view->form = $form;
        $this->view->course = $obj->getCourse();
    }

    public function deleteAction ()
    {
        $obj = $this->_service->find(
                $this->getRequest()
                    ->getParam('id'));
        if (! $this->_service->isAllowed($obj, 'acl_delete')) {
            throw new \exception("permission Denied.");
        }
        
        $form = $this->_service->getDeleteForm();
        if ($this->_request->isPost()) {
            $courseId = $obj->getCourse()->getId();
            
            if ($this->_service->acl_delete($obj->getId())) {
                $this->_helper->flashMessenger($this->_service->getMessage());
                $this->_redirect('/course/view/' . $courseId);
            }
        }
        $this->view->form = $form;
    }
}

