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

class Zencoder extends \Zend_Validate_Abstract
{

    const PERMISSION_DENIED = 'permissionDenied';

    const API_KEY_READ_ONLY = 'apiKeyIsReadOnly';

    const OTHER_PROBLEM = 'otherProblem';

    const INTEGRATION_MODE_ENABLED = 'integrationModeEnabled';

    const ACCOUNT_NOT_ACTIVE = 'accountNotActive';

    const BILLING_NOT_ACTIVE = 'billingNotActive';

    protected $_messageTemplates = array(
            self::PERMISSION_DENIED => "The API Key provided appears to be incrorrect.",
            self::API_KEY_READ_ONLY => "The API key provided must have read/write capabilities.",
            self::OTHER_PROBLEM => "Zencoder had the following problem: %value%",
            self::INTEGRATION_MODE_ENABLED => "The Zencoder account is currently in integration mode. It must be in live mode for audio/video encoding to work properly.",
            self::ACCOUNT_NOT_ACTIVE => "Your Zencoder account is not active. Please validate that it is operational.",
            self::BILLING_NOT_ACTIVE => "Your Zencoder billing is not active. Please validate that the billing information is correct."
    );

    public

    function isValid ($value, $context = null)
    {
        $this->_setValue($value);

        // 1. check account details

        try {
            $zencoder = new \Services_Zencoder($value);
            $accountDetails = $zencoder->accounts->details();
        } catch (\Exception $e) {
            $this->_setValue($e->getMessage());
            $this->_error(self::OTHER_PROBLEM);
            return false;
        }
        if (! $accountDetails) {
            $this->_error(self::PERMISSION_DENIED);
            return false;
        } else {
            if ($accountDetails->integration_mode) {
                $this->_error(self::INTEGRATION_MODE_ENABLED);
                return false;
            }

            if ($accountDetails->account_state != "active") {
                $this->_error(self::ACCOUNT_NOT_ACTIVE);
                return false;
            }

            if ($accountDetails->billing_state != "active") {
                $this->_error(self::BILLING_NOT_ACTIVE);
                return false;
            }
            return true;
        }
    }
}