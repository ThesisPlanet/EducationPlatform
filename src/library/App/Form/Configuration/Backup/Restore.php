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
namespace App\Form\Configuration\Backup;

class Restore extends \Zend_Form
{

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

        $file = new \Zend_Form_Element_File('file');
        $file->setLabel('File')
            ->setRequired(true)
            ->setMaxFileSize('209715200')
            ->addValidator('MimeType', false,
                array(
                        'application/x-gzip',
                        'application/octet-stream'
                ));

        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Restore from this tar.gz file')->setOptions(
                array(
                        'class' => 'btn btn-danger'
                ));
        // Attach elements to the form
        $this->addElement($id)
            ->addElement($file)
            ->addElement($submit);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}