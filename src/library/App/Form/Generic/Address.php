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
namespace App\Form\Generic;
class Address extends \Zend_Form
{
    public function init ()
    {
        $id = new \Zend_Form_Element_Hidden('id');
        $id->addFilter('StringTrim')->addFilter('HtmlEntities');
        /*
         * Form Elements & Other Definitions Here ...
         */
        // initialize form
        // $this->setAction('/Admin/customer/edit')
        $this->setMethod('post');
        $this->setOptions(array('class' => 'form-stacked'));
        // create text input for e-mail address
        $name = new \Zend_Form_Element_Text('name');
        $name->setLabel('Name/ATTN')
            ->setRequired(true)
            ->addfilter('StringTrim')
            ->addfilter('HtmlEntities');
        $company = new \Zend_Form_Element_Text('company');
        $company->setLabel('Company')
            ->setRequired(false)
            ->addfilter('StringTrim')
            ->addfilter('HtmlEntities');
        $address1 = new \Zend_Form_Element_Text('address1');
        $address1->setLabel('Address 1')
            ->setRequired(true)
            ->addfilter('StringTrim')
            ->addfilter('HtmlEntities');
        $address2 = new \Zend_Form_Element_Text('address2');
        $address2->setLabel('Address 2')
            ->setRequired(false)
            ->addfilter('StringTrim')
            ->addfilter('HtmlEntities');
        $city = new \Zend_Form_Element_Text('city');
        $city->setLabel('City')
            ->setRequired(true)
            ->addfilter('StringTrim')
            ->addfilter('HtmlEntities');
        $state = new \Zend_Form_Element_Text('state');
        $state->setLabel('State')
            ->setRequired(true)
            ->addfilter('StringTrim')
            ->addfilter('HtmlEntities');
        $zipCode = new \Zend_Form_Element_Text('zipCode');
        $zipCode->setLabel('Zip Code')
            ->setRequired(true)
            ->addfilter('StringTrim')
            ->addfilter('HtmlEntities')
            ->addValidator('Digits');
        $country = new \Zend_Form_Element_Text('country');
        $country->setLabel('Country')
            ->setRequired(true)
            ->addfilter('StringTrim')
            ->addfilter('HtmlEntities');
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit Changes')->setOptions(
        array('class' => 'btn primary'));
        // Attach elements to the form
        $this->addElement($id)
            ->addElement($name)
            ->addElement($company)
            ->addElement($address1)
            ->addElement($address2)
            ->addElement($city)
            ->addElement($state)
            ->addElement($zipCode)
            ->addElement($country)
            ->addElement($submit);
    }
    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}