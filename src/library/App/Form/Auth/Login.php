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
namespace App\Form\Auth;

class Login extends \Zend_Form
{

    public function init ()
    {
        
        // initialize form
        $this->setAction('/login')->setMethod('post');
        $this->setOptions(
                array(
                        'class' => 'login_form'
                ));
        // create text input for e-mail address
        /**
         * $this->addElement('ValidationTextBox', 'email',
         * array(
         * 'label' => "Email",
         * 'required' => true,
         * 'regExp' => '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$',
         * 'invalidMessage' => "Please provide a valid e-mail address."
         * ));
         */
        
        $email = new \Zend_Form_Element_Text('email');
        $email->setLabel('E-mail Address: ')
            ->setOptions(
                array(
                        'class' => 'login_email_image'
                ))
            ->setRequired(true)
            ->addValidator('emailAddress')
            ->addFilter('StringTrim');
        // create text input for e-mail address
        $password = new \Zend_Form_Element_Password('password');
        $password->setLabel('Password: ')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(
                array(
                        'class' => "login_password_image"
                ));
        
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Log In')->setOptions(
                array(
                        'class' => 'login_btn'
                ));
        // Attach elements to the form
        $this->addElement($email)
            ->addElement($password)
            ->addElement($submit);
    }
}