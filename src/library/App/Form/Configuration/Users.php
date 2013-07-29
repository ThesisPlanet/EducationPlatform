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
namespace App\Form\Configuration;

class Users extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setOptions(
                array(
                        'class' => 'form-stacked'
                ));

        // SSL Certficates

        $canRegister = new \Zend_Form_Element_Radio('canRegister');
        $canRegister->setLabel('Anyone can register a new account')
            ->setDescription(
                "This will allow anyone who can access the platform to be able to create a new account and subscribe to courses.
                    If disabled, users can only be added by a platform administrator.")
            ->setMultiOptions(
                array(
                        "1" => "Yes",
                        "0" => "No"
                ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');

        $canCreateCourses = new \Zend_Form_Element_Radio('canCreateCourses');
        $canCreateCourses->setLabel(
                'Anyone with an account can create a new course')
            ->setDescription(
                "This grants any user to create a new course that will be visible to other users.
                    Selecting No restricts the ability to create a new course to administrators (who can then invite a user to a newly created course and then grant them the 'provider' role)")
            ->setMultiOptions(
                array(
                        "1" => "Yes",
                        "0" => "No"
                ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');

        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Update settings')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        $this->addElement($canRegister)
            ->addElement($canCreateCourses)
            ->addElement($submit);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}