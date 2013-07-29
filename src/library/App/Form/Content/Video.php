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
namespace App\Form\Content;

class Video extends \Zend_Form
{

    protected $categoryOptions = array();

    public function init ()
    {
        $this->setMethod('post');
        $this->setAttrib('enctype', 'multipart/form-data');
        $this->setOptions(
                array(
                        'class' => 'form-stacked'
                ));
        $id = new \Zend_Form_Element_Hidden('id');
        $id->addFilter('StringTrim')->addFilter('HtmlEntities');
        
        $course = new \Zend_Form_Element_Text('course');
        $course->setLabel('course')
            ->setOptions(array(
                'size' => '50'
        ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $convertedSizeKB = new \Zend_Form_Element_Text('convertedSizeKB');
        $convertedSizeKB->setLabel('converted size in Kilobytes')
            ->setOptions(array(
                'size' => '50'
        ))
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $description = new \Zend_Form_Element_Textarea('description');
        $description->setLabel('Description')
            ->setRequired(false)
            ->setOptions(
                array(
                        'class' => 'span8',
                        'rows' => '4'
                ))
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        
        $file = new \Zend_Form_Element_File('file');
        $file->setLabel('File')
            ->setRequired(true)
            ->setMaxFileSize('209715200')
            ->addValidator('MimeType', false, 
                array(
                        'application/annodex',
                        'application/mp4',
                        'application/ogg',
                        'application/vnd.rn-realmedia',
                        'application/x-matroska',
                        'video/3gpp',
                        'video/3gpp2',
                        'video/annodex',
                        'video/divx',
                        'video/flv',
                        'video/h264',
                        'video/mp4',
                        'video/mp4v-es',
                        'video/mpeg',
                        'video/mpeg-2',
                        'video/mpeg4',
                        'video/ogg',
                        'video/ogm',
                        'video/quicktime',
                        'video/ty',
                        'video/vdo',
                        'video/vivo',
                        'video/vnd.rn-realvideo',
                        'video/vnd.vivo',
                        'video/webm',
                        'video/x-bin',
                        'video/x-cdg',
                        'video/x-divx',
                        'video/x-dv',
                        'video/x-flv',
                        'video/x-la-asf',
                        'video/x-m4v',
                        'video/x-matroska',
                        'video/x-motion-jpeg',
                        'video/x-ms-asf',
                        'video/x-ms-dvr',
                        'video/x-ms-wm',
                        'video/x-ms-wmv',
                        'video/x-msvideo',
                        'video/x-sgi-movie',
                        'video/x-tivo',
                        'video/avi',
                        'video/x-ms-asx',
                        'video/x-ms-wvx',
                        'video/x-ms-wmx'
                ));
        
        $originalExtension = new \Zend_Form_Element_Text('originalExtension');
        $originalExtension->setLabel('File extension')
            ->setOptions(array(
                'size' => '50'
        ))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $originalSizeKB = new \Zend_Form_Element_Text('originalSizeKB');
        $originalSizeKB->setLabel('original size in Kilobytes')
            ->setOptions(array(
                'size' => '50'
        ))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $isPublished = new \Zend_Form_Element_Radio('isPublished');
        $isPublished->setLabel('Published')
            ->addMultiOptions(
                array(
                        0 => "No",
                        1 => "Yes"
                ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $isEnabled = new \Zend_Form_Element_Radio('isEnabled');
        $isEnabled->setLabel('Enabled')
            ->addMultiOptions(
                array(
                        0 => "No",
                        1 => "Yes"
                ))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $role = new \Zend_Form_Element_Radio('role');
        $role->setLabel('Who can view this?')
            ->addMultiOptions(
                array(
                        'user' => "Any registered user",
                        'subscriber' => "Subscribers",
                        'provider' => 'Platform Managers'
                ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $title = new \Zend_Form_Element_Text('title');
        $title->setLabel('Title')
            ->setOptions(array(
                'class' => 'span8'
        ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit Changes')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($id)
            ->addElement($title)
            ->addElement($description)
            ->addElement($file)
            ->addElement($course)
            ->addElement($isPublished)
            ->addElement($isPublished)
            ->addElement($role)
            ->addElement($originalExtension)
            ->addElement($originalSizeKB)
            ->addElement($convertedSizeKB)
            ->addElement($submit);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}


