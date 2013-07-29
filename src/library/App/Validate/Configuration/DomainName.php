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

class DomainName extends \Zend_Validate_Abstract
{

    const UNRESOLVABLE = 'unresolvable';

    const NO_CONNECT = 'noConnect';

    const WRONG_SYSTEM = 'wrongSystem';

    const INVALID_ADDRESS = 'invalidAddress';

    protected $_messageTemplates = array(
            self::UNRESOLVABLE => "Unable to look up %value%. Please validate that DNS has been configured. Global DNS changes can take up to 72 hours to update.",
            self::NO_CONNECT => "Unable to connect to %value% on port 80 (HTTP)",
            self::WRONG_SYSTEM => "%value% does not appear to be an instance of the Digital Education Platform",
            self::INVALID_ADDRESS => "%value% does not pass as a valid URL"
    );

    public function isValid ($value)
    {
        $this->_setValue($value);

        $url = "http://" . $value;
/**
        if (strtolower($value) == "localhost") {
            $this->_error(self::INVALID_ADDRESS);
            return false;
        }**/

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            $this->_error(self::INVALID_ADDRESS);
            return false;
        }

        if (gethostbyname($value) == $value) {
            $this->_error(self::UNRESOLVABLE);
            return false;
        }

        // make the connection with curl
        $cl = curl_init($url);
        curl_setopt($cl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($cl, CURLOPT_HEADER, true);
        curl_setopt($cl, CURLOPT_NOBODY, true);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);

        // get response
        $response = curl_exec($cl);

        curl_close($cl);

        if (! $response) {
            $this->_error(self::NO_CONNECT);
            return false;
        }
        // Platform validation
        // page /system/dep_validate.html should exist and return a 200 OK
        $testUrl = $url . "/system/dep_validate.html";
        $cl = curl_init($testUrl);
        curl_setopt($cl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($cl, CURLOPT_HEADER, true);
        curl_setopt($cl, CURLOPT_NOBODY, true);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);

        // get response
        $response = curl_exec($cl);
        $statusCode = curl_getinfo($cl, CURLINFO_HTTP_CODE);
        curl_close($cl);

        if (! $response) {
            $this->_error(self::NO_CONNECT);
            return false;
        } else {
            if ($statusCode == 200) {
                return true;
            } else {
                $this->_error(self::WRONG_SYSTEM);
                return false;
            }
        }

        return true;

        // TODO: Add system checking
    }
}