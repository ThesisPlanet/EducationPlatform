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

class Course extends \Zend_Acl
{

    public function __construct ()
    {
        $this->setRoles();
        $this->setPrivileges();
        
        if (\Zend_Registry::getInstance()->isRegistered('users')) {
            $options = \Zend_Registry::getInstance()->get('users');
            if($options->canCreateCourses !== true)
            {
                $this->removeAllow('user', null, 'acl_create');
                $this->allow('admin', null, 'acl_create');
            }
        }
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
        $this->allow('visitor', null,
                array(
                        'acl_getThumbnailUrl',
                        'acl_findSubscriptions',
                        'acl_getImageUrl'
                ));
        $this->allow('user', null,
                array(
                        'acl_findMySubscriptions',
                        'acl_create',
                        'acl_subscribe',
                        'acl_findSubscription'
                ));
        $this->allow('subscriber', null,
                array(
                        'acl_completeContent',
                        'acl_uncompleteContent',
                        'acl_getCompletedContentList',
                        'acl_getPercentComplete',
                        'acl_rate',
                        'acl_comment',
                        'acl_addReview',
                        'acl_unsubscribe',
                        'acl_listAnnouncements',
                        'acl_listContentOrder',
                        'acl_findAnnouncement',
                        'acl_listChapters'
                ));
        $this->allow('provider', null,
                array(
                        'acl_update',
                        'acl_manageContent',
                        'acl_addCoupon',
                        'acl_disableCoupon',
                        'acl_publish',
                        'acl_unpublish',
                        'acl_addChapter',
                        'acl_deleteChapter',
                        'acl_providerFindSubscription',
                        'acl_providerUpdateSubscriptionRole',
                        'acl_providerDeleteSubscription',
                        'acl_providerSubscribeUser',
                        'acl_providerCreateAnnouncement',
                        'acl_providerRemoveAnnouncement',
                        'acl_updateImage',
                        'acl_providerUpdateContentSort',
                        'acl_updateChapterOrder'
                ));
        $this->allow('admin', null,
                array(
                        'acl_delete',
                        'acl_removeReview',
                        'acl_enable',
                        'acl_disable',
                        'acl_removeCoupon'
                ));
        return true;
    }
}