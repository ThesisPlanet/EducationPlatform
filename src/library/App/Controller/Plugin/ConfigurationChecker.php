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
namespace App\Controller\Plugin;

class ConfigurationChecker extends \Zend_Controller_Plugin_Abstract {
	public function PreDispatch(\Zend_Controller_Request_Abstract $request) {
		// User must be logged in.
		if (\Zend_Auth::getInstance ()->hasIdentity ()) {
			if (strtolower ( $request->getModuleName () ) == "initialization" || strtolower ( $request->getModuleName () ) == "shared" || strtolower ( $request->getControllerName () ) == "error" || strtolower ( $request->getControllerName () ) == "notauthorized") {
			} else {
				$redirector = \Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
				$redirector->gotoUrl ( '/Initialization' );
				$redirector->redirectAndExit ();
			}
		}
	}
}