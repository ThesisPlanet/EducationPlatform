<?php
class Admin_IndexController extends Zend_Controller_Action
{
    public function init ()
    {
        $this->view->page = "Admin";
        /*
         * Initialize action controller here
         */
    }
    public function indexAction ()
    {}
}

