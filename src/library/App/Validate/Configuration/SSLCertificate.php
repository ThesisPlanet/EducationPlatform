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
namespace App\Validate\Configuration;

/**
 *
 * @author Jack Peterson
 *         Validates a SSL Certificate.
 */
class SSLCertificate extends \Zend_Validate_Abstract
{

    const MISSING_DATA = 'missingDataSet';

    const UNABLE_TO_OPEN_PUBLIC_CERT = 'unableToOpenPublicCert';

    const UNABLE_TO_OPEN_PRIVATE_KEY = 'unableToOpenPrivateKey';

    const UNABLE_TO_OPEN_BUNDLE = 'unableToOpenBundle';

    const CERT_DOES_NOT_MATCH_KEY = 'certDoesNotMatchKey';

    const CERT_IS_NOT_A_CERTIFICATE = 'certIsNotACert';

    const BUNDLE_IS_NOT_A_CERTIFICATE = 'bundleIsNotACeter';

    protected $_messageTemplates = array(
            self::MISSING_DATA => "Additional information such as the secret key must be provided to test connectivity",
            self::UNABLE_TO_OPEN_PUBLIC_CERT => "Unable to open the public certificate file.",
            self::UNABLE_TO_OPEN_PRIVATE_KEY => "Unable to open the private key.",
            self::UNABLE_TO_OPEN_BUNDLE => "Unable to open the bundle file.",
            self::CERT_DOES_NOT_MATCH_KEY => "the Certificate file does not match the provided private key",
            self::CERT_IS_NOT_A_CERTIFICATE => "It appears that the file uploaded as the public certificate is not a valid SSL certificate file",
            self::BUNDLE_IS_NOT_A_CERTIFICATE => "The bundle certificate provided is not valid."
    );

    public function isValid ($value, $context = null)
    {
        $this->_setValue($value);

        if (is_array($context)) {

            if (! isset($_FILES['publicKey'])) {
                $this->_error(self::UNABLE_TO_OPEN_PUBLIC_CERT);
                return false;
            }
            if (! isset($_FILES['privateKey'])) {
                $this->_error(self::UNABLE_TO_OPEN_PRIVATE_KEY);
                return false;
            }
            if (! isset($_FILES['bundle'])) {
                // not critical...
            } else {}
        } else {
            $this->_error(self::MISSING_DATA);
            return false;
        }

        try {
            // load up each file

            // Load the private key

            $privateKeyString = file_get_contents(
                    $_FILES['privateKey']['tmp_name']);
            $certificateString = file_get_contents(
                    $_FILES['publicKey']['tmp_name']);

            $certificate = openssl_x509_parse($certificateString);

            if (! is_array($certificate)) {
                $this->_error(self::CERT_IS_NOT_A_CERTIFICATE);
                return false;
            }

            if (! openssl_x509_check_private_key($certificateString,
                    $privateKeyString)) {
                $this->_error(self::CERT_DOES_NOT_MATCH_KEY);
                return false;
            }

            // OK, the private key matches the public key

            if (array_key_exists('bundle', $_FILES)) {
                // Bundle was provided. It needs to be joined into the
                // certificate string to support NGINX.
                $bundleString = file_get_contents($_FILES['bundle']['tmp_name']);
                $bundle = openssl_x509_parse($bundleString);

                if (! is_array($certificate)) {
                    $this->_error(self::BUNDLE_IS_NOT_A_CERTIFICATE);
                    return false;
                }
            }
            return true;
            // start testing the certs
        } catch (\exception $e) {
            $this->_error(self::OTHER_PROBLEM . $e->getMessage());
            return false;
        }
    }
}