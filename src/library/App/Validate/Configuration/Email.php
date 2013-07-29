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

class Email extends \Zend_Validate_Abstract {

	const PERMISSION_DENIED = 'permissionDenied';

	const OTHER_PROBLEM = 'otherProblem';

	const NO_CONTEXT = 'noContext';

	protected $_messageTemplates = array(self::PERMISSION_DENIED => "There was a problem logging in.", self::OTHER_PROBLEM => "The following exception was generated: %value%", self::NO_CONTEXT => "The context must be provided.");

	public function isValid($value, $context = null) {
		$this->_setValue($value);

		// 1. check account details

		if (!isset($context) && !is_array($context)) {
			$this->_error(self::NO_CONTEXT);
			return false;
		} else {
			$options = array('auth' => $context['authMode'], 'username' => $context['username'], 'password' => $context['password'], 'ssl' => $context['ssl'], 'port' => $context['port']);
		}

		try {
			$mail = new \TP\Communication\Email(null, $context['server'], $options);
			$mail->setSubject("DEP - Server test email")->addTo($context['destination']);
			$mail->sendHtmlTemplate("testEmail.phtml");
		} catch (\Exception $e) {
			$this->_setValue($e->getMessage());
			$this->_error(self::OTHER_PROBLEM);
			return false;
		}

		return true;
	}
}
