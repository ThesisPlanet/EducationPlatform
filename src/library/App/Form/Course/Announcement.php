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

class Announcement extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setOptions(
                array(
                        'class' => 'form-stacked'
                ));
        $text = new \Zend_Form_Element_Text('text');
        $text->setLabel('Your question...')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(array(
                'class' => 'span8'
        ));
        $notifyUsers = new \Zend_Form_Element_Radio('notify');
        $notifyUsers->setLabel("Notify subscribers?");
        
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('SHOUT it out!')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($text)->addElement($submit);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}