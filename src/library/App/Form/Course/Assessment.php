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
namespace App\Form\Course;

class Assessment extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setOptions(
                array(
                        'class' => 'form-stacked'
                ));
        $title = new \Zend_Form_Element_Text('title');
        $title->setLabel('Please give this assessment a title.')
            ->setDescription(
                "Titles help you and your subscribers know what this assessment is about.")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(array(
                'class' => 'span8'
        ));
        
        $description = new \Zend_Form_Element_Textarea("description");
        $description->setLabel("Go ahead, describe this assessment.")
            ->setDescription(
                "You might want to let your subscribers know what they should study prior to taking this assessment!")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(
                array(
                        'class' => 'span8'
                ));
        
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Add this assessment!')->setOptions(
                array(
                        'class' => 'btn btn-primary btn-large'
                ));
        // Attach elements to the form
        $this->addElement($title)
            ->addElement($description)
            ->addElement($submit);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}