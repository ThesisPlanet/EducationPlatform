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
namespace App\Service;

class Configuration extends Base {

	protected $_em = null;

	protected $_form = null;

	protected $_forgotPasswordForm = null;

	protected $_resetPasswordForm = null;

	protected $_deleteForm = null;

	protected $_acl = null;

	public function __construct() {
		$this->_em = \Zend_Registry::get('em');
		$this->_acl = new \App\Service\ACL\Configuration();
	}

	const NOT_FOUND = "not_found";

	const WRONG_PASSWORD = "wrong_password";

	const NOT_ACTIVATED = "not_activated";

	const CERTS_DONT_MATCH = "The certificates provided do not appear to match up.";

	public function isAllowed($userObj, $action) {
		if (!isset($this->_user)) {
			throw new \exception(self::USER_NOT_SET);
		}
		if (!is_object($this->_user)) {
			throw new \exception(self::USER_NOT_OBJECT);
		}
		if (!method_exists($this->_user, 'getSubscriptions')) {
			throw new \exception(self::USER_MUST_IMPLEMENT_GETSUBSCRIPTIONS);
		}
		$userService = new \App\Service\User();
		if (!is_object($userObj)) {
			$role = $userService->authorize($this->_user->getId());
			if ($this->_acl->isAllowed($role, null, $action)) {
				return true;
			} else {
				return false;
			}
		} else {
			if (!method_exists($userObj, 'getId')) {
				throw new \exception(self::INVALID_PROTECTED_OBJECT);
			}
			$role = $userService->authorize($this->_user->getId());
			if ($this->_acl->isAllowed($role, null, $action)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Gets the user's role-override.
	 *
	 * @param integer $userId            
	 * @throws \exception
	 * @return string
	 */
	public function authorize($userId) {
		$userObj = $this->find($userId);
		if (is_object($userObj)) {
			$specialRole = $userObj->getRole();
			if ($specialRole != null) {
				return (string) $specialRole;
			} else {
				return \App\Acl\Roles::VISITOR;
			}
		} else {
			throw new \exception(self::NOT_FOUND);
		}
	}

	public function acl_getImageUrl($options = array()) {
		$opt = array();
		if (file_exists(SHARE_PATH . DIRECTORY_SEPARATOR . "logo.png")) {
			$opt['Secure'] = false;
			if (!isset($options['minutes'])) {
				$options['minutes'] = null;
				$numberOfMinutes = 30;
			} else {
				$numberOfMinutes = (int) $options['minutes'];
			}
			if (!isset($options['IPAddress'])) {
			} else {
				$opt['IPAddress'] = $options['IPAddress'];
			}
			$filename = "configuration/logo.png";
			try {
				if (class_exists("AmazonCloudFront")) {
					$cf = new \AmazonCloudFront();
					if (\Zend_Registry::getInstance()->isRegistered('cloud')) {
						$distribution_hostname = \Zend_Registry::getInstance()->get('cloud')->aws->CLOUDFRONT_DOWNLOAD_DISTRIBUTION_URL;

						// Options should include restrictors such as IP
						// Address,
						// Duration,
						// etc.
						$expires = strtotime('+' . $numberOfMinutes . 'minutes'); // time
						$url = $cf->get_private_object_url($distribution_hostname, $filename, $expires, $opt);

						return $url;
					} else {
						return "/images/logo.png";
					}
				} else {
					return "/images/logo.png";
				}
			} catch (\exception $e) {
				$logger = \Zend_Registry::get('logger');
				$logger->log("Service/Configuration/acl_getImageUrl" . $e->getMessage(), \Zend_Log::ERR);
			}
		} else {
			$imgUrl = "/images/logo.png";
		}

		return $imgUrl;
	}

	public function acl_updateImage($options = array()) {
		if (!$this->isAllowed($this->_user, __FUNCTION__)) {
			throw new \exception(self::PERMISSION_DENIED);
		}
		$form = new \App\Form\Configuration\UpdateImage();

		$data = array();

		if ($form->isValid($data)) {

			$oldname = \pathinfo($form->file->getFileName());

			$newname = SHARE_PATH . DIRECTORY_SEPARATOR . "logo_tmp." . $oldname['extension'];

			$finalname = SHARE_PATH . DIRECTORY_SEPARATOR . "logo.png";

			$form->file->addFilter('Rename', array('target' => $newname, 'overwrite' => true));

			$form->getValues();

			$form->file->getTransferAdapter()->setOptions(array('useByteString' => false));

			// Resize the image

			$i = new \TP\Misc\Image($finalname);
			$i->resizeImage(234, 76, 'crop');
			$i->saveImage($finalname);
			$form->uploadImage($finalname);

			$this->_message('update_success');
			return true;
		} else {
			throw new \exception(self::FORM_INVALID);
		}
	}

	/**
	 * Moves the existing certificate to .
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 * bak files. Saves the new certificate information into the appropriate
	 * location.
	 *
	 * @return boolean
	 * @throws exception
	 */
	public function acl_updateSSLCertificates($certificateString, $privateKeyString) {
		if (!$this->isAllowed($this->_user, __FUNCTION__)) {
			throw new \exception(self::PERMISSION_DENIED);
		}

		// Doube check that the Cert is OK
		if (!openssl_x509_check_private_key($certificateString, $privateKeyString)) {
			throw new \exception(self::CERTS_DONT_MATCH);
		}

		// CERTIFICATE (PUBLIC KEY)
		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "public.crt")) {
			if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "public.crt.bak")) {
				unlink(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "public.crt.bak");
			}
			rename(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "public.crt", CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "public.crt.bak");
		}
		$fp = fopen(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "public.crt", "w");
		$result = fwrite($fp, $certificateString);
		if (!$result) {
			throw new \exception("Unable to save the public certificate.");
		}
		fclose($fp);

		// PRIVATE-KEY

		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "private.key")) {
			if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "private.key.bak")) {
				unlink(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "private.key.bak");
			}
			rename(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "private.key", CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "private.key.bak");
		}
		$fp = fopen(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "private.key", "w");
		$result = fwrite($fp, $privateKeyString);

		if (!$result) {
			throw new \exception("Unable to save private key.");
		}

		fclose($fp);

		// OK. files were written and all appear to be valid SSL certs. Time to redirect NGINX + restart it.

		exec("mv -f /etc/nginx/ssl/dep_ssl-rproxy.pem /etc/nginx/ssl/dep_ssl-rproxy.pem.old");
		exec("mv -f /etc/nginx/ssl/dep_ssl-rproxy.key /etc/nginx/ssl/dep_ssl-rproxy.key.old");
		exec("ln -s " . CONFIGURATION_PATH . "/private.key" . " /etc/nginx/ssl/dep_ssl-rproxy.key");
		exec("ln -s " . CONFIGURATION_PATH . "/public.crt" . " /etc/nginx/ssl/dep_ssl-rproxy.pem");
		exec("sudo /etc/init.d/nginx configtest 2>&1 &", $output);
		if (array_key_exists(0, $output)) {
			if (preg_match("/emerg/", $output[0])) {
				// Problems... undo the LN's
				throw new \exception(var_export($output, true));
			} else {
				exec("sudo /etc/init.d/nginx restart");
				sleep(5);
				echo "OK!";
			}
		}

		return true;
	}
}
