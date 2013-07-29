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

/**
 *
 * @author Jack Peterson
 *         CFDownload validator will attempt to download the temporary file that
 *         was created during initial S3 testing. returning a 200 OK is the
 *         expected result. Anything other than that will equate to a failure.
 */
class CFStreaming extends \Zend_Validate_Abstract
{

    const PERMISSION_DENIED = 'permissionDenied';

    const NOT_OWNER = 'invalidAddress';

    const MISSING_DATA = 'missingDataSet';

    const OTHER_ERROR = 'otherError';

    const DISTRIBUTION_NOT_FOUND = 'distributionNotFound';

    const BUCKET_DNS_NOT_MATCH_CF = 'bucketDNSNotMatchCF';

    const DOMAIN_NOT_MATCH = 'domainNotMatch';

    const CNAME_NOT_MATCH = 'cnameNotMatch';

    const KEYPAIR_NOT_MATCH = 'keypairNotMatch';

    const DISTRIBUTION_NOT_DEPLOYED = 'distributionNotDeployed';

    protected $_messageTemplates = array(
            self::PERMISSION_DENIED => "Permission was denied to AWS or the bucket based on the Access Key or Secret Key provided.",
            self::NOT_OWNER => "You must be the owner of that bucket",
            self::MISSING_DATA => "Additional information such as the secret key must be provided to test connectivity",
            self::OTHER_ERROR => "Other error: %value%",
            self::DISTRIBUTION_NOT_FOUND => "The provided Streaming Distribution ID was not found. Please double check that it it correct.",
            self::BUCKET_DNS_NOT_MATCH_CF => "The DNS Name for the bucket does not match what is referenced by that Streaming distribution.",
            self::DOMAIN_NOT_MATCH => "The Domain Name configured on CloudFront does not match what was provided for the CF Streaming distribution URL",
            self::CNAME_NOT_MATCH => "The CNAME configured on CloudFront does not match what was provided for the CF Streaming distribution URL.",
            self::KEYPAIR_NOT_MATCH => "The KeyPair ID does not match what CloudFront Streaming distribution is configured for",
            self::DISTRIBUTION_NOT_DEPLOYED => "The streaming distribution is not yet deployed. If you just created it, it may take a few minutes to become available."
    );

    public function isValid ($value, $context = null)
    {
        $this->_setValue($value);

        if (is_array($context)) {
            if (! isset($context['secretKey'])) {
                $this->_error(self::MISSING_SECRET_KEY);
                return false;
            }
        } else {
            $this->_error(self::MISSING_DATA);
            return false;
        }

        // Do the CF related stuff

        // Override credential from the registry
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
        $cf = new \AmazonCloudFront();
        $s3 = new \AmazonS3();

        $distribution_hostname = $context['CFDownloadDistributionURL'];

        $distributionResponse = $cf->get_distribution_info(
                $context['CFStreamingDistributionId'],
                array(
                        'Streaming' => true
                ));

        if (isset($distributionResponse->body))
            $distributionInfo = $distributionResponse->body;

        if (! $distributionInfo) {
            $this->_error(self::DISTRIBUTION_NOT_FOUND);
            return false;
        }

        if ($distributionInfo->Status != "Deployed") {
            $this->_error(self::DISTRIBUTION_NOT_DEPLOYED);
            return false;
        }

        $bucketDNS = $context['bucket'] + ".s3.amazonaws.com";
        if ($distributionInfo->StreamingDistributionConfig->S3Origin->DNSName !=
                 $bucketDNS) {
            $this->_error(self::BUCKET_DNS_NOT_MATCH_CF);
            return false;
        }
        $urlArr = explode("/", $context['CFDistributionStreamer']);
        if ($distributionInfo->StreamingDistributionConfig->CNAME) {
            if ($urlArr[0] !=
                     $distributionInfo->StreamingDistributionConfig->CNAME) {
                $this->_error(self::CNAME_NOT_MATCH);
                return false;
            }
        } else {
            if ($urlArr[0] != $distributionInfo->DomainName) {
                $this->_error(self::DOMAIN_NOT_MATCH);
                return false;
            }
        }

        if ($context['CFKeypairId'] !=
                 $distributionInfo->ActiveTrustedSigners->Signer->KeyPairId) {
            $this->_error(self::KEYPAIR_NOT_MATCH);
            return false;
        }

        return true;
    }
}