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

class UpdateImage extends \Zend_Form
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
                        'image/png',
                        'image/jpeg',
                        'application/octet-stream'
                ));

        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Add or replace course image')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($id)
            ->addElement($file)
            ->addElement($submit);
    }

    public function convertImage ($oldName, $newName)
    {
        // Convert the image
        $convertedData = $this->imageToPng($oldName, 250);
        $f = fopen($newName, 'w+');
        $data = fwrite($f, $convertedData);
        fclose($f);

        return true;
    }

    public function uploadImage ($finalName)
    {
        $cloud = new \App\Service\Core\Cloud();
        if ($cloud->uploadFile($finalName,
                "course/image/" . $this->id->getValue() . ".png")) {
            $cloud->setACL("course/image/" . $this->id->getValue() . ".png");
        }

        return true;
    }

    /*
     * Resizes an image and converts it to PNG returning the PNG data as a
     * string
     */
    function imageToPng ($srcFile, $maxSize = 100)
    {
        list ($width_orig, $height_orig, $type) = getimagesize($srcFile);

        // Get the aspect ratio
        $ratio_orig = $width_orig / $height_orig;

        $width = $maxSize;
        $height = $maxSize;

        // resize to height (orig is portrait)
        if ($ratio_orig < 1) {
            $width = $height * $ratio_orig;
        }         // resize to width (orig is landscape)
        else {
            $height = $width / $ratio_orig;
        }

        switch ($type) {
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($srcFile);
                break;
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($srcFile);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($srcFile);
                break;
            default:
                throw new \Exception('Unrecognized image type ' . $type);
        }

        // create a new blank image
        $newImage = imagecreatetruecolor($width, $height);

        // Copy the old image to the new image
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height,
                $width_orig, $height_orig);

        // Output to a temp file
        $destFile = tempnam(
                SHARE_PATH . DIRECTORY_SEPARATOR . "course" . DIRECTORY_SEPARATOR .
                         "image_tmp", "pic");
        imagepng($newImage, $destFile);

        // Free memory
        imagedestroy($newImage);

        if (is_file($destFile)) {
            $f = fopen($destFile, 'rb');
            $data = fread($f, filesize($destFile));
            fclose($f);

            // Remove the tempfile
            unlink($destFile);
            return $data;
        }

        throw new \Exception('Image conversion failed. - ' . $destFile);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}