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

class Question extends \Zend_Acl
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
        $this->allow('subscriber', null,
                array(
                        'acl_askCourseQuestion',
                        'acl_answerCourseQuestion',
                        'acl_listCourseQuestions',
                        'acl_listCourseQuestionAnswers',
                        'acl_askContentQuestion',
                        'acl_answerContentQuestion',
                        'acl_listContentQuestions',
                        'acl_listContentQuestionAnswers',
                        'acl_findCourseQuestion',
                        'acl_findContentQuestion'
                ));
        $this->allow('provider', null,
                array(
                        'acl_removeCourseQuestion',
                        'acl_removeCourseQuestionAnswer',
                        'acl_removeContentQuestion'
                ));
        return true;
    }
}