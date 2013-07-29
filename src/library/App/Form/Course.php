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
namespace App\Form;

class Course extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setOptions(
                array(
                        'class' => 'form-stacked'
                ));
        // create text input for e-mail address
        $topic = new \Zend_Form_Element_Text('topic');
        $topic->setLabel('Topic')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(array(
                'class' => 'span8'
        ));
        
        $title = new \Zend_Form_Element_Text('title');
        $title->setLabel('Title')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(array(
                'class' => 'span8'
        ));
        
        $isApprovalRequired = new \Zend_Form_Element_Radio('isApprovalRequired');
        $isApprovalRequired->setLabel(
                'Do you want to approve user access requests?')
            ->addMultiOptions(
                array(
                        0 => "No",
                        1 => "Yes"
                ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        
        $isSearchable = new \Zend_Form_Element_Radio('isSearchable');
        $isSearchable->setLabel(
                'Should this course be visible in search results once it is published?')
            ->addMultiOptions(
                array(
                        0 => "No",
                        1 => "Yes"
                ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        
        $isPublished = new \Zend_Form_Element_Radio('isPublished');
        $isPublished->setLabel(
                'Publishing this course makes it visible to others -- unless you make this not visible in search.')
            ->addMultiOptions(
                array(
                        0 => "No",
                        1 => "Yes"
                ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        
        $description = new \Zend_Form_Element_Text('description');
        $description->setLabel('Description')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(array(
                'class' => 'span8'
        ));
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit Changes')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($topic)
            ->addElement($title)
            ->addElement($isApprovalRequired)
            ->addElement($isSearchable)
            ->addElement($description)
            ->addElement($submit);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}