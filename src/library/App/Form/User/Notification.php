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
namespace DEP\Form\Core\User;

class Notification extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $password = new \Zend_Form_Element_Password('password');
        $password->setLabel('Password')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $password2 = new \Zend_Form_Element_Password('password2');
        $password2->setLabel('Please confirm your password')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit Changes')->setOptions(
                array(
                        'class' => 'submit'
                ));
        $this->addElement($password)->addElement($submit);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}