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
namespace App\Service\Core;

/**
 * Internal Application service to send files from anywhere on the Local FS to
 * cloud
 *
 * @author Jack.Peterson
 */
class Cloud extends \App\Service\Base
{

    protected $_defaultMapper;

    public function __construct ()
    {}

    public function deleteFile ($filename)
    {
        $file = $filename;
        
        $bucket = \Zend_Registry::getInstance()->get('cloud')->aws->BUCKET;
        $s3 = new \AmazonS3();
        $delete = $s3->delete_object($bucket, $file);
        if ($delete->isOK()) {
            $logger = \Zend_Registry::get('logger');
            $logger->log("CLOUD/$filename deleted.\n", \Zend_Log::INFO);
            return TRUE;
        } else {
            throw new \exception("Failed to delete $filename.\n");
            return FALSE;
        }
    }

    /**
     * uploads a file located in SHARE_PATH / localsource to Cloud Destination
     *
     * @param string $localsource            
     * @param string $destination            
     * @return boolean
     */
    public function uploadFile ($localsource, $destination)
    {
        $file = $localsource;
        $logger = \Zend_Registry::get('logger');
        if (file_exists($localsource) == TRUE) {
            $s3 = new \AmazonS3();
            $bucket = \Zend_Registry::getInstance()->get('cloud')->aws->BUCKET;
            if ($s3->if_bucket_exists($bucket) == true) {
                $s3->batch()->create_object($bucket, $destination, 
                        array(
                                'fileUpload' => $file
                        ));
                $file_upload_response = $s3->batch()->send();
                if ($file_upload_response->areOK()) {
                    $logger->log("Upload for $destination Succeeded.", 
                            \Zend_Log::INFO);
                    return TRUE;
                } else {
                    $logger->log("FAILED to upload $destination.", 
                            \Zend_Log::INFO);
                    throw new \exception("file upload to cloud failed");
                    return FALSE;
                }
            } else {
                throw new \exception(
                        "Bucket not found: " .
                                 \Zend_Registry::getInstance()->get('cloud')->aws->BUCKET);
            }
        } else {
            throw new \exception("File not found at $file");
        }
    }

    public function setACL ($file)
    {
        $s3 = new \AmazonS3();
        $response = $s3->set_object_acl(
                \Zend_Registry::getInstance()->get('cloud')->aws->BUCKET, $file, 
                array(
                        array(
                                'id' => \Zend_Registry::getInstance()->get(
                                        'cloud')->aws->CLOUDFRONT_CANONICAL_ID,
                                'permission' => \AmazonS3::GRANT_READ
                        ), // CloudFront,
                           // READ
                        array(
                                'id' => \Zend_Registry::getInstance()->get(
                                        'encoder')->zencoder->CANONICAL_ID,
                                'permission' => \AmazonS3::GRANT_READ
                        ), // Zencoder Read
                        array(
                                'id' => \Zend_Registry::getInstance()->get(
                                        'cloud')->aws->CANONICAL_ID,
                                'permission' => \AmazonS3::GRANT_FULL_CONTROL
                        )
                )); // Self,
                    // FULL_CONTROL
        if ($response->isOK() == true) {
            return TRUE;
        } else {
            throw new \exception(print_r($response, true));
            return FALSE;
        }
    }

    public function fetchFileSize ($filename)
    {
        $s3 = new \AmazonS3();
        $size = $s3->get_object_filesize(
                \Zend_Registry::getInstance()->get('cloud')->aws->BUCKET, 
                $filename) / 1024;
        return $size;
    }
}