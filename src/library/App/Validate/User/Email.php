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
class Email extends \Zend_Validate_Abstract
{
    const NOT_AVAILABLE = 'notAvailable';
    protected $_messageTemplates = array(
    self::NOT_AVAILABLE => "%value% is already registered. Try logging in instead!");
    public function isValid ($value)
    {
        $this->_setValue($value);
        $userService = new \App\Service\User();
        $user = $userService->findByEmail($value);
        if (is_object($user)) {
            $this->_error(self::NOT_AVAILABLE);
            return false;
        }
        return true;
    }
}