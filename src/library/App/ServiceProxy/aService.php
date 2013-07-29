<?php
namespace App\ServiceProxy;

abstract class aService
{

    protected $_service = null;

    public function __construct ()
    {
        $this->loadService();
        if (\Zend_Auth::getInstance()->hasIdentity()) {
            $this->_service->setUser(
                    \Zend_Auth::getInstance()->getIdentity()
                        ->getUser());
        }
    }
}