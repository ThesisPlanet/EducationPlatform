<?php
/**
 * Thesis Planet - Digital Education Platform
 *
 * LICENSE
 *
 * This source file is subject to the licensing terms found at http://www.thesisplanet.com/platform/tos
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to sales@thesisplanet.com so we can send you a copy immediately.
 *
 * @category  ThesisPlanet
 * @copyright  Copyright (c) 2009-2012 Thesis Planet, LLC. All Rights Reserved. (http://www.thesisplanet.com)
 * @license   http://www.thesisplanet.com/platform/tos   ** DUAL LICENSED **  #1 - Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License. #2 - Thesis Planet Commercial Use EULA.
 */
namespace App\Service\ACL;

class Subscription extends \Zend_Acl
{

    public function __construct ()
    {
        $this->setRoles();
        $this->setPrivileges();
    }

    protected function setRoles ()
    {
        $this->addRole('visitor');
        $this->addRole('system', 'visitor');
        $this->addRole('user', 'visitor');
        $this->addRole('subscriber', 'user');
        $this->addRole('provider', 'subscriber');
        $this->addRole('owner', 'provider');
        $this->addRole('admin', 'owner');
    }

    protected function setPrivileges ()
    {
        $this->allow('user', null, 
                array(
                        'acl_findSubscriptions',
                        'acl_update',
                        'acl_subscribe'
                ));
        
        $this->allow('admin', null, 
                array(
                        'acl_delete',
                        'acl_enable',
                        'acl_disable'
                )
                );
        return true;
    }
}