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

class Server extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setOptions(
                array(
                        'class' => 'form-stacked'
                ));

        // SSL Certficates

        $domain = new \Zend_Form_Element_Text('domain');
        $domain->setLabel('Domain Name')
            ->setDescription("example: training.yourcompany.com")
            ->setRequired(true)
            ->addValidator(new \App\Validate\Configuration\DomainName())
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(
                array(
                        'class' => 'span8'
                ));

        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Set Domain Name')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($domain)->addElement($submit);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}