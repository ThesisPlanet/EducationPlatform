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
namespace App\Validate\User;
class Username extends \Zend_validate_Abstract
{
    const NOT_AVAILABLE = 'notAvailable';
    const INVALID_USERNAME = 'invalidUsername';
    const STRING_EMPTY = 'stringEmpty';
    const RESERVED = 'reserved';
    protected $_messageTemplates = array(
    self::NOT_AVAILABLE => "'%value%' has already been taken. Please pick another username.", 
    self::INVALID_USERNAME => "'%value%' is not a valid username. A username must contain letters and/or numbers without any spaces.", 
    self::STRING_EMPTY => "Please provide a username.", 
    self::RESERVED => "a phrase in your username has been reserved to help prevent confusion.");
    public function isValid ($value)
    {
        $this->_setValue($value);
        if (trim($value == null)) {
            $this->_error(self::STRING_EMPTY);
            return false;
        }
        if (! is_string($value)) {
            $this->_error(self::INVALID_USERNAME);
            return false;
        }
        // reserved
        if (preg_match(
        "/admin|employee|owner|sysadmin|company|thesisplanet/i", $value)) {
            $this->_error(self::RESERVED);
            return false;
        }
        if (!preg_match("/^([a-zA-Z0-9._]+)$/", $value)) {
            $this->_error(self::INVALID_USERNAME);
            return false;
        }
        // defer database to the last minute (load reduction)
        $userService = new \App\Service\User();
        $user = $userService->findOneByUsername($value);
        if (is_object($user)) {
            $this->_error(self::NOT_AVAILABLE);
            return false;
        }
        return true;
    }
}
