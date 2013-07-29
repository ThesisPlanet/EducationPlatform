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
namespace App\Validate\Configuration\AWS;

class S3 extends \Zend_Validate_Abstract
{

    const PERMISSION_DENIED = 'permissionDenied';

    const NOT_OWNER = 'invalidAddress';

    const BUCKET_NOT_EXIST = 'bucketDoesNotExist';

    const MISSING_DATA = 'missingDataSet';

    const MISSING_SECRET_KEY = 'missingSecretKey';

    const MISSING_ACCESS_KEY = 'missingAccessKey';

    const UNABLE_TO_LIST_BUCKETS = 'unableToListBuckets';

    const MISSING_BUCKET_NAME = 'missingBucketName';

    const UPLOAD_FAILED = 'uploadFailed';

    const BUCKET_NOT_AVAILABLE = 'bucketNotAvailable';

    const UNABLE_TO_CREATE_BUCKET = 'unableToCreateBucket';

    const UNABLE_TO_SET_ACL = 'unableToSetACL';

    protected $_messageTemplates = array(
            self::PERMISSION_DENIED => "Permission was denied to AWS or the bucket based on the Access Key or Secret Key provided.",
            self::NOT_OWNER => "You must be the owner of that bucket",
            self::BUCKET_NOT_EXIST => "That bucket does not exist. Please sign in to the AWS Management Console and create it first.",
            self::MISSING_DATA => "Additional information such as the secret key must be provided to test connectivity",
            self::MISSING_SECRET_KEY => "The secret key must be provided.",
            self::MISSING_ACCESS_KEY => "The access key must be provided",
            self::UNABLE_TO_LIST_BUCKETS => "Unable to list buckets. Check that the access key and the secret key are correct.",
            self::MISSING_BUCKET_NAME => "The bucket name was not provided.",
            self::UPLOAD_FAILED => "Unable to write to that bucket.",
            self::BUCKET_NOT_AVAILABLE => "That bucket name has already been taken by someone other than you.",
            self::UNABLE_TO_CREATE_BUCKET => "A problem was encountered while trying to create a new bucket",
            self::UNABLE_TO_SET_ACL => "There was a problem setting the ACL Settings. Zencoder Settings must be set first. Also, validate that each canonical ID is correct."
    );

    public function isValid ($value, $context = null)
    {
        $this->_setValue($value);

        if (is_array($context)) {
            if (! isset($context['secretKey'])) {
                $this->_error(self::MISSING_SECRET_KEY);
                return false;
            }
            if (! isset($context['key'])) {
                $this->_error(self::MISSING_ACCESS_KEY);
                return false;
            }

            if (! isset($context['bucket'])) {
                $this->_error(self::MISSING_BUCKET_NAME);
                return false;
            }

            $s3 = new \Zend_Service_Amazon_S3($context['key'],
                    $context['secretKey']);

            \CFCredentials::set(
                    array(
                            'development' => array(
                                    'key' => $context['key'],
                                    'secret' => $context['secretKey'],
                                    'cloudfront_keypair' => $context['CFKeypairId'],
                                    'cloudfront_pem' => $context['CFPrivateKeyPEM'],
                                    'default_cache_config' => '',
                                    'certificate_authority' => false
                            ),
                            '@default' => 'development'
                    ));

            $awss3 = new \AmazonS3();

            $bucketList = $s3->getBuckets();
            if ($bucketList) {
                // Able to list buckets. Now, let's check to see if the bucket
                // is owned by this user.
                $ownedBucket = false;
                // throw new \exception(print_r($bucketList, true));
                foreach ($bucketList as $index => $bucketName) {
                    if ($bucketName == $context['bucket']) {
                        $ownedBucket = $bucketName;
                    }
                }
                if ($ownedBucket) {
                    // attempt a write test.
                    if (! $s3->putObject(
                            $context['bucket'] . "/" .
                                     "DEP_CONFIGURATION_TEST_FILE",
                                    "This file can be deleted at any point in time.")) {
                        $this->_error(self::UPLOAD_FAILED);
                        return false;
                    } else {
                        $response = $awss3->set_object_acl($context['bucket'],
                                "DEP_CONFIGURATION_TEST_FILE",
                                array(
                                        array(
                                                'id' => $context['CFCanonicalId'],
                                                'permission' => \AmazonS3::GRANT_READ
                                        ), // CloudFront,
                                           // READ
                                        array(
                                                'id' => \Zend_Registry::getInstance()->get(
                                                        'encoder')->zencoder->CANONICAL_ID,
                                                'permission' => \AmazonS3::GRANT_READ
                                        ), // Zencoder
                                           // Read
                                        array(
                                                'id' => $context['canonicalId'],
                                                'permission' => \AmazonS3::GRANT_FULL_CONTROL
                                        )
                                )); // Self,
                                    // FULL_CONTROL
                        if ($response->isOK() == true) {
                            sleep(2);
                            return TRUE;
                        } else {
                            $this->_error(self::UNABLE_TO_SET_ACL);
                            return FALSE;
                        }
                    }
                } else {
                    // If not, attempt to create it.
                    if ($s3->isBucketAvailable($context['bucket'])) {
                        $this->_error(self::BUCKET_NOT_AVAILABLE);
                        return false;
                    } else {
                        // S3 Zend Plugin (as of 1.1.2) doesn't work for some
                        // reason and is not providing anything useful in terms
                        // of debugging.
                        // Initialize the S3 SDK.

                        // OVERRIDE THE SETTINGS

                        $result = $awss3->create_bucket($context['bucket'],
                                \AmazonS3::REGION_US_E1);

                        // end of awss3.
                        if ($result) {
                            // wait 2 seconds.
                            sleep(2);
                            // attempt a write test.
                            if (! $s3->putObject(
                                    $context['bucket'] . "/" .
                                             "DEP_CONFIGURATION_TEST_FILE",
                                            "This file can be deleted at any point in time.")) {
                                $this->_error(self::UPLOAD_FAILED);
                                return false;
                            } else {
                                // Set the ACL on the file, then return true.

                                $response = $awss3->set_object_acl(
                                        $context['bucket'],
                                        "DEP_CONFIGURATION_TEST_FILE",
                                        array(
                                                array(
                                                        'id' => $context['CFCanonicalId'],
                                                        'permission' => \AmazonS3::GRANT_READ
                                                ), // CloudFront,
                                                   // READ
                                                array(
                                                        'id' => \Zend_Registry::getInstance()->get(
                                                                'encoder')->zencoder->CANONICAL_ID,
                                                        'permission' => \AmazonS3::GRANT_READ
                                                ), // Zencoder
                                                   // Read
                                                array(
                                                        'id' => $context['canonicalId'],
                                                        'permission' => \AmazonS3::GRANT_FULL_CONTROL
                                                )
                                        )); // Self,
                                            // FULL_CONTROL
                                if ($response->isOK() == true) {
                                    sleep(2);
                                    return TRUE;
                                } else {
                                    $this->_error(self::UNABLE_TO_SET_ACL);
                                    return FALSE;
                                }
                            }
                        } else {
                            throw new \exception(print_r($result, true));
                            $this->_error(self::UNABLE_TO_CREATE_BUCKET);
                            return false;
                        }
                    }
                }
            } else {
                $this->_error(self::UNABLE_TO_LIST_BUCKETS);
                return false;
            }
        } else {
            $this->_error(self::MISSING_DATA);
            return false;
        }

        // 1. get a list of buckets

        // 2. check to see if that bucket exists

        // 2.a. if not, create the bucket, if not, fail with an error indicating
        // that the bucket name is already claimed

        // 3. write to the bucket + set ACL
        // 3. a. if not, permisssion denied error
    }
}