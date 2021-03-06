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
namespace App\Form;

class Subscription extends \Zend_Form
{

    public function init ()
    {
        /**
         * private $id;
         * private $userId;
         * private $serviceId;
         * private $expires;
         * private $role;
         */
        $this->setMethod('post');
        // create text input for e-mail address
        $role = new \Zend_Form_Element_Select('role');
        $role->setLabel('Role')
            ->addMultiOptions(
                array(
                        'visitor' => 'visitor',
                        'user' => 'user',
                        'subscriber' => 'subscriber',
                        'provider' => 'provider'
                ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $course = new \Zend_Form_Element_Text('course');
        $course->setLabel('Course Id')
            ->setOptions(array(
                'size' => '50'
        ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $user = new \Zend_Form_Element_Text('user');
        $user->setLabel('User')
            ->setOptions(array(
                'size' => '50'
        ))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $id = new \Zend_Form_Element_Hidden('id');
        $id->setOptions(array(
                'size' => '50'
        ))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit Changes')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($id)
            ->addElement($user)
            ->addElement($role)
            ->addElement($course)
            ->addElement($submit);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}