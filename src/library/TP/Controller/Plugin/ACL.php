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
namespace TP\Controller\Plugin;

class ACL extends \Zend_Controller_Plugin_Abstract {
	public function preDispatch(\Zend_Controller_Request_Abstract $request) {
		$logger = \Zend_Registry::getInstance()->get('logger');
		$options = \Zend_Registry::getInstance()->get('aclConfig');
		// Check config file to see if 'aclplugin.settings.active' exists
		if (!isset($options->active)) {
			$logger->log("ACL is disabled!", \Zend_Log::CRIT);
		} else {
			if ($options->active == false) {
				$logger->log("ACL is intentionally disabled.", \Zend_Log::CRIT);
			} else {
				$module = strtolower($request->getModuleName());
				$controller = strtolower($request->controller);
				$action = strtolower($request->action);
				$secureUrl = false;
				if (!isset($options->module)) {
					$logger->log("No modules are set in the ACL!", \Zend_Log::CRIT);
				} else {
					// Check module
					if (isset($options->module->$module->require_acl)) {
						$secureUrl = ($options->module->$module->require_acl) ? true : false;
						$layer = "module";
					}
					// Check Controller
					if (isset($options->module->$module->$controller->require_acl)) {
						$secureUrl = ($options->module->$module->$controller->require_acl) ? true : false;
						$layer = "controller";
					}
					// Check Action
					if (isset($options->module->$module->$controller->$action->require_acl)) {
						$secureUrl = ($options->module->$module->$controller->$action->require_acl) ? true : false;
						$layer = "action";
					}
					if ($secureUrl === false) {
						$logger->log("Permitting because $module,$controller,$action does not require ACL.", \Zend_Log::DEBUG);
					} else {
						// if an ACL is required.
						// Check if the user is permitted
						$auth = \Zend_Auth::getInstance();
						$storageObj = $auth->getStorage()->read();
						if (isset($storageObj) && is_object($storageObj)) {
							$roleName = $storageObj->getRoleId();
						} else {
							$roleName = 'visitor';
						}
						$acl = \Zend_Registry::get('Zend_Acl');
						try {
							if (!$acl->isAllowed($roleName, $module . "_" . $controller, $action)) {
								// echo "Permission denied -- Rolename:
								// $roleName, Module: $module, Resource:
								// $controller, Privilege: $action";
								$request->setModuleName('Site');
								$request->setControllerName('Error');
								$request->setActionName('notAuthorized');
								$logger->log("Permission denied: $roleName,$module,$controller,$action", \Zend_Log::DEBUG);
								return false;
							} else {
								$logger->log("Permission granted: $roleName,$module,$controller,$action", \Zend_Log::DEBUG);
								return true;
							}
						} catch (\exception $e) {
							$logger->log($e->getMessage(), \Zend_Log::ALERT);
							$request->setModuleName('Site');
							$request->setControllerName('Error');
							$request->setActionName('notAuthorized');
							$logger->log("Permission denied: $roleName,$module,$controller,$action", \Zend_Log::DEBUG);
							return false;
						}
					}
				}
			}
		}
	}
}
