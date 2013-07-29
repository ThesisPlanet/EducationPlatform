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
class CFDownload extends \Zend_Validate_Abstract
{

    const PERMISSION_DENIED = 'permissionDenied';

    const NOT_OWNER = 'invalidAddress';

    const MISSING_DATA = 'missingDataSet';

    const URL_GENERATOR_FAILED = 'urlGenerationFailed';

    const UNABLE_TO_FETCH_FILE = 'unableToFetchFile';

    protected $_messageTemplates = array(
            self::PERMISSION_DENIED => "Permission was denied to AWS or the bucket based on the Access Key or Secret Key provided.",
            self::NOT_OWNER => "You must be the owner of that bucket",
            self::MISSING_DATA => "Additional information such as the secret key must be provided to test connectivity",
            self::URL_GENERATOR_FAILED => "Unable to generate a download URL",
            self::UNABLE_TO_FETCH_FILE => "CloudFront Download Distribution did not return a 200 OK. Please Check that your PEM, keypair ID, and cloudfront Download Distribution are properly set up. Generated url: %value%"
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

        // generate a url

        $opt['Secure'] = false;
        if (! isset($options['minutes'])) {
            $options['minutes'] = null;
            $numberOfMinutes = 30;
        } else {
            $numberOfMinutes = (int) $options['minutes'];
        }
        if (! isset($options['IPAddress'])) {} else {
            $opt['IPAddress'] = $options['IPAddress'];
        }
        $filename = "DEP_CONFIGURATION_TEST_FILE";
        $cf = new \AmazonCloudFront();
        $distribution_hostname = $context['CFDownloadDistributionURL'];
        // Options should include restrictors such as IP Address, Duration, etc.
        $expires = strtotime('+' . $numberOfMinutes . 'minutes'); // time
        try {
            $url = $cf->get_private_object_url($distribution_hostname,
                    $filename, $expires, $opt);
            $this->_value = $url;
            // Hit the URL
            $config = array(
                    'adapter' => 'Zend_Http_Client_Adapter_Socket'
            );
            $httpClient = new \Zend_Http_Client($url, $config);
            $httpResponse = $httpClient->request(\Zend_Http_Client::GET);
            if ($httpResponse->getStatus() == 200) {
                return true;
            } else {
                $this->_error(self::UNABLE_TO_FETCH_FILE);
                return false;
            }
        } catch (\exception $e) {
            $this->_error(self::URL_GENERATOR_FAILED);
            return false;
        }
    }
}