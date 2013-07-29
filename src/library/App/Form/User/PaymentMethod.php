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
class PaymentMethod extends \Zend_Form
{
    public function init ()
    {
        $this->setMethod('post');
        $this->setOptions(array('class' => 'form-stacked'));
        $name = new \Zend_Form_Element_Text('name');
        $name->setLabel('Name on card')
            ->setRequired(true)
            ->addFilter('StringTrim');
        $address1 = new \Zend_Form_Element_Text('address_1');
        $address1->setLabel('Address 1')
            ->setRequired(true)
            ->addFilter('StringTrim');
        $address2 = new \Zend_Form_Element_Text('address_2');
        $address2->setLabel('Address 2 (if applicable)')
            ->setRequired(false)
            ->addFilter('StringTrim');
        $city = new \Zend_Form_Element_Text('city');
        $city->setLabel('city')
            ->setRequired(true)
            ->addFilter('StringTrim');
        $state = new \Zend_Form_Element_Text('state');
        $state->setLabel('state')
            ->setRequired(true)
            ->addFilter('StringTrim');
        $zipCode = new \Zend_Form_Element_Text('zipCode');
        $zipCode->setLabel('Zip Code')
            ->setRequired(true)
            ->addFilter('StringTrim');
        $cardType = new \Zend_Form_Element_Select('cardType');
        $cardType->addMultiOptions(
        array('visa' => "Visa", 'mc' => "MasterCard"))
            ->setRequired(true)
            ->addFilter('StringTrim');
        $cardNumber = new \Zend_Form_Element_Text('cardNumber');
        $cardNumber->setLabel('payment card number')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator('CreditCard');
        $expirationMonth = new \Zend_Form_Element_Select('expirationMonth');
        $expirationMonth->setLabel('expiration month')
            ->setRequired(true)
            ->addMultiOptions(
        array('01' => "01 / January", '02' => "02 / February", 
        '03' => "03 / March", '04' => "04 / April", '05' => "05 / May", 
        '06' => "06 / June", '07' => "07 / July", '08' => "08 / August", 
        '09' => "09 / September", '10' => "10 / October", 
        '11' => "11 / November", '12' => "12 / December"))
            ->addFilter('StringTrim')
            ->addValidator(
        new \Zend_Validate_Between(array('min' => 1, 'max' => 12)));
        $expirationYear = new \Zend_Form_Element_Select('expirationYear');
        $expirationYear->setLabel('expiration year')
            ->setRequired(true)
            ->addMultiOptions(
        array('12' => "2012", "13" => "2013", '14' => "2014", '15' => "2015", 
        '16' => "2016", '17' => "2017", '18' => "2018", '19' => "2019", 
        '20' => "2020"))
            ->addFilter('StringTrim')
            ->addValidator(
        new \Zend_Validate_Between(array('min' => 12, 'max' => 40)));
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save / Continue')->setOptions(
        array('class' => 'submit'));
        $this->addElement($name)
            ->addElement($address1)
            ->addElement($address2)
            ->addElement($city)
            ->addElement($state)
            ->AddElement($zipCode)
            ->addElement($cardNumber)
            ->addElement($cardType)
            ->addElement($expirationMonth)
            ->addElement($expirationYear)
            ->addElement($submit);
    }
    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}