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
namespace App\Form\User;
class ForgotPassword extends \Zend_Form
{
    public function init ()
    {
        $this->setMethod('post');
        $this->setOptions(array('class' => 'form-stacked'));
        $email = new \Zend_Form_Element_Text('email');
        $email->setLabel('email')
            ->setRequired(true)
            ->addValidator('emailAddress')
            ->addFilter('StringTrim');
        
        //$this->addElement($captcha);
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit Changes')->setOptions(
        array('class' => 'submit'));
        
        $this->addElement('captcha', 'captcha', array(
        		'label'      => 'Please enter the 5 letters displayed below:',
        		'required'   => true,
        		'captcha'    => array(
        				'captcha' => 'Figlet',
        				'wordLen' => 5,
        				'timeout' => 300
        		)
        ));
        
        
        $this->addElement($email)
            ->addElement($submit);
    }
    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}