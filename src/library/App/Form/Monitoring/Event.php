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
namespace App\Form\Monitoring;
class Event extends \Zend_Form
{
    public function init ()
    {
        $this->setMethod('post');
        //create text input for e-mail address
        
        $id = new \Zend_Form_Element_Hidden('id');
        $id->setOptions(array('size' => '50'))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $deviceName = new \Zend_Form_Element_Text('deviceName');
        $deviceName->setLabel('Device Name')
            ->setOptions(array('size' => '50'))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $deviceIp = new \Zend_Form_Element_Text('deviceIp');
        $deviceIp->setLabel('Device IP Address')
            ->setOptions(array('size' => '50'))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');    
            
        $message = new \Zend_Form_Element_Textarea('message');
        $message->setLabel('Message')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $priority = new \Zend_Form_Element_text('priority');
        $priority->setLabel('Priority (0 = highest')
            ->setRequired(true)
            ->addFilter('StringTrim');
        
        $priorityName = new \Zend_Form_Element_Text('priorityName');
        $priorityName->setLabel('Priority Name')
            ->setOptions(array('size' => '50'))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $timestamp = new \Zend_Form_Element_Text('timestamp');
        $timestamp->setLabel('Timestamp')
            ->setOptions(array('size' => '50'))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
            
            
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit Changes')->setOptions(
        array('class' => 'submit'));
        //Attach elements to the form
        $this->addElement($id)
            ->addElement($deviceIp)
            ->addElement($deviceName)
            ->addElement($message)
            ->addElement($priority)
            ->addElement($priorityName)
            ->addElement($timestamp)
            ->addElement($submit);
    }
    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}