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
namespace App\Form\Queue;
class Encoder extends \Zend_Form
{
    public function init ()
    {
        $this->setMethod('post');
        //create text input for e-mail address
        $objId = new \Zend_Form_Element_Text('objId');
        $objId->setLabel('Media object Id')
            ->setOptions(array('size' => '50'))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $objType = new \Zend_Form_Element_Text('objType');
        $objType->setLabel('Media object type')
            ->setOptions(array('size' => '50'))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $status = new \Zend_Form_Element_Text('status');
        $status->setLabel('Status')
            ->setOptions(array('size' => '50'))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $jobId = new \Zend_Form_Element_Text('jobId');
        $jobId->setLabel('Job Id')
            ->setOptions(array('size' => '50'))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $errorMessage = new \Zend_Form_Element_Textarea('errorMessage');
        $errorMessage->setLabel('Error Message')
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $percentComplete = new \Zend_Form_Element_Text('percentComplete');
        $percentComplete->setLabel('Percent Complete')
            ->setOptions(array('size' => '50'))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $jobType = new \Zend_Form_Element_Text('jobType');
        $jobType->setLabel('Job type')
            ->setOptions(array('size' => '50'))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $id = new \Zend_Form_Element_Hidden('id');
        $id->setOptions(array('size' => '50'))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit Changes')->setOptions(
        array('class' => 'submit'));
        //Attach elements to the form
        $this->addElement($id)
            ->addElement($objId)
            ->addElement($objType)
            ->addElement($status)
            ->addElement($jobId)
            ->addElement($percentComplete)
            ->addElement($errorMessage)
            ->addElement($jobType)
            ->addElement($submit);
    }
    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}