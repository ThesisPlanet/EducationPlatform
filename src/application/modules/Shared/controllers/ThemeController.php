<?php
class Shared_ThemeController extends Zend_Controller_Action
{
    public function init ()
    {
        $this->_helper->layout->disableLayout();
    }
    public function cssAction ()
    {
        if (! Zend_Registry::isRegistered('service')) {
            $this->getResponse()->setHeader('Content-type', 'text/css');
            $this->view->css = Zend_Registry::get('channel')->getCss();
            
        } else {
            $this->view->headStyle()->setStyle(
            Zend_Registry::get('service')->getCss(), $attributes = array());
        }
    }
}


