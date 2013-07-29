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
class ContentACL
{

    protected $acl;

    /**
     * Creates an ACL for a specific piece of content
     * 
     * @param string $type            
     * @param integer $id            
     * @return Zend_Acl
     */
    public function createAcl ($obj)
    {
        $role = $obj->getRole();
        $this->acl = new Zend_Acl();
        // resources
        $this->acl->add(new \Zend_Acl_Resource($obj->getId()));
        // roles
        $this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::VISITOR));
        $this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::USER), 
                \App\Acl\Roles::VISITOR);
        $this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::SUBSCRIBER), 
                \App\Acl\Roles::VISITOR);
        $this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::PROVIDER), 
                \App\Acl\Roles::SUBSCRIBER);
        $this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::ADMIN), 
                \App\Acl\Roles::PROVIDER);
        
        foreach ($privileges as $privilege) {
            $acl->addRole(new Zend_Acl_Role($privilege->getUserId()));
            $acl->allow($privilege->getUserId(), $page->getId());
        }
        return $acl;
    }
}