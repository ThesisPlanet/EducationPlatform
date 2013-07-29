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
namespace App\ContentAcl;

class Generator
{

    protected $acl;

    public function __construct ()
    {
        $this->acl = new \Zend_Acl();
    }

    public function create ($resource)
    {
        if (is_object($resource)) {
            if (method_exists($resource, "getRole")) {
                if ($resource->getRole() != null) {
                    $this->setRoles();
                    $this->setPrivileges($resource->getRole());
                } else {
                    throw new \exception(
                            "\\App\\ContentAcl\\Generator:createAcl - The role on the provided resource object is null.");
                }
                // The method exists, now let's check to see what role is
                // required & build an ACL
                return true;
            } else {
                throw new \exception("The GetRoleId() method must exist.");
            }
        } else {
            throw new \exception(
                    "App\\ContentAcl\\Generator - I cannot create an ACL on a non-object.");
        }
    }

    protected function setRoles ()
    {
        // depends on App/Acl/roles.
        $this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::VISITOR));
        $this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::USER), 
                \App\Acl\Roles::VISITOR);
        $this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::SUBSCRIBER), 
                \App\Acl\Roles::VISITOR);
        $this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::PROVIDER), 
                \App\Acl\Roles::SUBSCRIBER);
        $this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::ADMIN), 
                \App\Acl\Roles::PROVIDER);
    }
    // dynamically sets privileges based on the passed in role
    protected function setPrivileges ($minAuthorizedRole)
    {
        switch ($minAuthorizedRole) {
            // anyone can access the content:
            case \App\Acl\Roles::VISITOR:
                $this->acl->allow(\App\Acl\Roles::VISITOR, null, 
                        array(
                                'acl_view'
                        ));
                break;
            // USERS and higher can access the content
            case \App\Acl\Roles::USER:
                $this->acl->allow(\App\Acl\Roles::USER, null, 
                        array(
                                'acl_view'
                        ));
                break;
            // Subscribers and higher can access the content
            case \App\Acl\Roles::SUBSCRIBER:
                $this->acl->allow(\App\Acl\Roles::SUBSCRIBER, null, 
                        array(
                                'acl_view'
                        ));
                break;
            // Providers and higher can access the content
            case \App\Acl\Roles::PROVIDER:
                $this->acl->allow(\App\Acl\Roles::PROVIDER, null, 
                        array(
                                'acl_view'
                        ));
                break;
            // Admins and higher can access the content
            case \App\Acl\Roles::ADMIN:
                $this->acl->allow(\App\Acl\Roles::ADMIN, null, 
                        array(
                                'acl_view'
                        ));
                break;
            default:
                throw new \exception(
                        "\\App\\ContentAcl\\Generator:setPrivileges - unknown role provided: '$minAuthorizedRole'. no privileges have been set.");
        }
    }

    public function getAcl ()
    {
        return $this->acl;
    }
}