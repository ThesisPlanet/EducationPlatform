<?php

class Initialization_IndexController extends Zend_Controller_Action {

	public function init() {
	}

	public function indexAction() {
		$this->view->page = "Platform Initialization";
		$status = array();

		// Check for configuration files

		// If they exist, then the assumption is that portion of the system is
		// properly configured.

		/**
		 * AWS
		 */

		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'cloud.ini')) {
			$config = new \Zend_Config_Ini(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'cloud.ini', APPLICATION_ENV);
			if (isset($config->provider)) {
				$status['aws'] = "OK";
			} else {
				$status['aws'] = "NOT OK";
			}
		} else {
			$status['aws'] = "Missing Configuration File";
		}

		/**
		 * Server
		 */

		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'system.ini')) {
			$status['server'] = "OK";
		} else {
			$status['server'] = "Missing Configuration File";
		}

		/**
		 * Server/SSL
		 */

		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "public.crt") && file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "private.key")) {
			$status['ssl'] = "OK";
		} else {
			$status['ssl'] = "Missing SSL Certificate";
		}
		/**
		 * Picture
		 */
		if (file_exists(SHARE_PATH . DIRECTORY_SEPARATOR . 'logo.png')) {
			$status['picture'] = "Displaying your logo!";
		} else {
			$status['picture'] = "Displaying the default picture";
		}

		/**
		 * Zencoder
		 */

		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'encoder.ini')) {
			$status['zencoder'] = "OK";
		} else {
			$status['zencoder'] = "Missing Configuration File";
		}

		/**
		 * Email
		 */

		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'communication.ini')) {
			$config = new \Zend_Config_Ini(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'communication.ini', APPLICATION_ENV);
			if (isset($config->email)) {
				$status['email'] = "OK";
			} else {
				$status['email'] = "NOT OK";
			}
		} else {
			$status['email'] = "Missing Configuration File";
		}

		/**
		 * Admin Account exists
		 */

		$userService = new \App\Service\User();
		$tmpAdmin = $userService->findByEmail('custAdmin@thesisplanet.com');

		if (is_object($tmpAdmin)) {
			$status['admin'] = "Warning: Temporary Admin account still exists.";
		} else {
			$status['admin'] = "OK";
		}

		/**
		 * User settings
		 */

		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'users.ini')) {
			$config = new \Zend_Config_Ini(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'users.ini', APPLICATION_ENV);
			if (isset($config->canRegister)) {
				$status['users'] = "OK";
			} else {
				$status['users'] = "NOT OK";
			}
		} else {
			$status['users'] = "Missing Configuration File";
		}

		if ($status['aws'] == "OK" && $status['server'] == "OK" && $status['zencoder'] == "OK" && $status['email'] == "OK" && $status['ssl'] == "OK" && $status['email'] == "OK") {
			if (!file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'configured.ini')) {
				$handle = fopen(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'configured.ini', "w+");
				$heading = "[" . APPLICATION_ENV . "]\n";
				fwrite($handle, $heading, strlen($heading));
				fclose($handle);
			}
		} else {

			if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'configured.ini')) {
				unlink(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'configured.ini');
			}
		}
		$this->view->status = $status;
	}
}

