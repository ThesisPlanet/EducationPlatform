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

class SSL extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setOptions(
                array(
                        'class' => 'form-stacked'
                ));

        /**
         * Need to receive a few files:
         * 1.
         * public key
         * 2. .key pricate key
         * 3. .crt - possibly a bundle certificate for providers like GoDaddy
         */

        $publicKey = new \Zend_Form_Element_File('publicKey');
        $publicKey->setLabel(
                'Public Key (certificate file) - typically ending in .crt')
            ->setRequired(true)
            ->setMaxFileSize('209715200');

        $privateKey = new \Zend_Form_Element_File('privateKey');
        $privateKey->setLabel(
                'Private Key - typically ending in .key')
            ->setRequired(true)
            ->setMaxFileSize('209715200');

        $bundle = new \Zend_Form_Element_File('bundle');
        $bundle->setLabel('Bundle certificates from provider')
            ->setDescription(
                "Some SSL providers like GoDaddy provide a certificate bundle, \"gd_bundle.crt\"")
            ->setRequired(false)
            ->setMaxFileSize('209715200');

        $certValidate = new \Zend_Form_Element_Hidden("certValidate");
        $certValidate->addValidator(
                new \App\Validate\Configuration\SSLCertificate())
            ->setValue("runValidator")
            ->setAutoInsertNotEmptyValidator(false)
            ->setRequired(true);

        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit Changes')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($privateKey)
            ->addElement($publicKey)
            ->addElement($bundle)
            ->addElement($certValidate)
            ->addElement($submit);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}