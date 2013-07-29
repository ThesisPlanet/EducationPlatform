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

class User extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        // create text input for e-mail address
        $username = new \Zend_Form_Element_Text('username');
        $username->setLabel('Pick a Username:')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $activated = new \Zend_Form_Element_Radio('activated');
        $activated->setLabel(
                'Activated - Has this user validated their e-mail address?')
            ->addMultiOptions(
                array(
                        0 => "No",
                        1 => "Yes"
                ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $email = new \Zend_Form_Element_Text('email');
        $email->setLabel('Email Address:')
            ->setOptions(array(
                'size' => '50'
        ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->addValidator('emailAddress');
        $firstname = new \Zend_Form_Element_Text('firstname');
        $firstname->setLabel('First Name:')
            ->setRequired(true)
            ->addFilter('StringTrim');
        $lastname = new \Zend_Form_Element_Text('lastname');
        $lastname->setLabel('Last Name:')
            ->setRequired(true)
            ->addFilter('StringTrim');
        
        $password = new \Zend_Form_Element_Password('password');
        $password->setLabel('New Password:')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $role = new \Zend_Form_Element_Select('role');
        $role->setLabel('Role')
            ->setDescription(
                "This is a system-wide override and is useful for testing functionality without needing to subscribe to perform most actions. The two main roles are User and Admin. Administrators can do everything. You have been warned.")
            ->setRequired(true)
            ->addMultiOptions(
                array(
                        'user' => 'User',
                        'subscriber' => 'Subscriber',
                        'provider' => 'Provider',
                        'admin' => 'Administrator'
                ))
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $id = new \Zend_Form_Element_Hidden('id');
        $id->setOptions(array(
                'size' => '50'
        ))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit Changes')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($id)
            ->addElement($firstname)
            ->addElement($lastname)
            ->addElement($email)
            ->addElement($username)
            ->addElement($password)
            ->addElement($role)
            ->addElement($activated)
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
                "user/image/" . $this->id->getValue() . ".png")) {
            $cloud->setACL("user/image/" . $this->id->getValue() . ".png");
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
        
        if (! is_dir(SHARE_PATH . DIRECTORY_SEPARATOR . "user")) {
            mkdir(SHARE_PATH . DIRECTORY_SEPARATOR . "user");
        }
        
        // Output to a temp file
        $destFile = tempnam(
                SHARE_PATH . DIRECTORY_SEPARATOR . "user" . DIRECTORY_SEPARATOR .
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