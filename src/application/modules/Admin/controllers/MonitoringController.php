<?php
class Admin_MonitoringController extends Zend_Controller_Action
{
    public function init ()
    {
    
    }
    public function indexAction ()
    {
        $service = new \App\Service\Monitoring\Event();
        $this->view->eventList = $service->findRecentEvents(
        $this->_request->getParam('page'), $this->_request->getParam('count'));
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
}

