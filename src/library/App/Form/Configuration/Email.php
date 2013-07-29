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

class Email extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setOptions(
                array(
                        'class' => 'form-stacked'
                ));

        $destination = new \Zend_Form_Element_Text('destination');
        $destination->setLabel("Please enter your e-mail address")
            ->setDescription(
                "The system will attempt to send a test e-mail to this address.")
            ->setRequired(true)
            ->addValidator('EmailAddress')
            ->setOptions(array(
                'class' => 'span8'
        ));

        $server = new \Zend_Form_Element_Text('server');
        $server->setLabel(
                'Server FQDN (fully qualified domain name) or IP Address')
            ->setDescription(
                "SMTP Server address example: smtp.yourcompany.com")
            ->setRequired(true)
            ->addValidator('Hostname')
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(array(
                'class' => 'span8'
        ));

        $port = new \Zend_Form_Element_Text('port');
        $port->setLabel('Port')
            ->setDescription(
                "The port that the system connects to. Google Apps: 465")
            ->setRequired(true);

        $authMode = new \Zend_Form_Element_Radio('authMode');
        $authMode->setLabel("SMTP Authentication Mode")
            ->setDescription(
                "Your email administrator shoud be able to provide you with this information")
            ->setMultiOptions(
                array(
                        'plain' => "Plain",
                        'login' => "Login",
                        'crammd5' => "CRAM-MD5"
                ))
            ->setRequired(true);

        $username = new \Zend_Form_Element_Text('username');
        $username->setLabel('Username')
            ->setRequired(true)
            ->addFilter('StringTrim');

        $password = new \Zend_Form_Element_Text('password');
        $password->setLabel('Password')
            ->setRequired(true)
            ->addFilter('StringTrim');

        $ssl = new \Zend_Form_Element_Radio('ssl');
        $ssl->setMultiOptions(
                array(
                        'ssl' => "SSL",
                        'tls' => "TLS"
                ))
            ->setLabel("Transport Security mode")
            ->setDescription(
                "Your SMTP provider should require encryption. Google uses SSL")
            ->setRequired(true)
            ->addFilter('StringTrim');

        $EmailValidate = new \Zend_Form_Element_Hidden("emailValidate");
        $EmailValidate->addValidator(new \App\Validate\Configuration\Email())
            ->setValue("run")
            ->setAutoInsertNotEmptyValidator(false)
            ->setRequired(true);

        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save Configuration')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($destination)
            ->addElement($server)
            ->addElement($port)
            ->addElement($username)
            ->addElement($password)
            ->addElement($ssl)
            ->addElement($authMode)
            ->addElement($EmailValidate)
            ->addElement($submit);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}