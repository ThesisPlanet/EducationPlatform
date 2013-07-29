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

class User extends \App\Service\Base implements \App\Service\iUser {

	protected $_em = null;

	protected $_form = null;

	protected $_forgotPasswordForm = null;

	protected $_resetPasswordForm = null;

	protected $_deleteForm = null;

	protected $_acl = null;

	public function __construct() {
		$this->_em = \Zend_Registry::get('em');
		$this->_repository = $this->_em->getRepository('\App\Entity\User');
		$this->_acl = new \App\Service\ACL\User();
	}

	const NOT_FOUND = "not_found";

	const WRONG_PASSWORD = "wrong_password";

	const NOT_ACTIVATED = "not_activated";

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

	public function authenticate($email, $password) {
		$user = $this->findByEmail($email);
		if (is_object($user)) {
			if ($user->getActivated() == false || $user->getActivated() == "0") {
				throw new \exception(self::NOT_ACTIVATED);
			}
			if ($user->getPassword() == hash('sha512', $password)) {
				// if ($user->getPassword() == hash('sha512', $password)) {
				return $user;
			} else {
				throw new \exception(self::WRONG_PASSWORD);
			}
		} else {
			throw new \exception(self::NOT_FOUND);
		}
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

	/**
	 *
	 * @param $email unknown_type
	 * @return \App\Entity\User
	 */
	public function register(array $data) {
		$form = $this->getForm();
		$form->setSubmitLabel("Create");
		$form->getElement('username')->addValidator(new \App\Validate\User\Username());
		$form->getElement('email')->addValidator(new \App\Validate\User\Email());
		$form->removeElement('role');
		$form->removeElement('activated');
		if ($form->isValid($data)) {
			$obj = new \App\Entity\User();
			$obj->setUsername($form->getValue('username'));
			$obj->setEmail($form->getValue('email'));
			$obj->setPassword($form->getValue('password'));
			$obj->setFirstname($form->getValue('firstname'));
			$obj->setLastname($form->getValue('lastname'));
			$obj->setRole('user');
			$obj->setActivated(0);
			try {
				$this->_em->persist($obj);
				$this->_em->flush();
				$this->_message('create_success');
				return $obj->getId();
			} catch (\exception $e) {
				echo "Unable to create. " . $e->getMessage();
				return false;
			}
		} else {
			throw new \exception("Form is invalid. cannot add a new user.");
		}
	}

	/**
	 *
	 * @param integer $id
	 * @param array $data
	 * @throws \exception
	 * @return boolean
	 */
	public function acl_admin_update($id, array $data) {
		$form = $this->getForm();
		$form->setSubmitLabel("Update");
		$obj = $this->_repository->find($id);

		if ($data['email'] != $obj->getEmail()) {
			$form->getElement('email')->addValidator(new \App\Validate\User\Email());
		}
		if (is_object($obj)) {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::NOT_FOUND);
		}
		if ($form->isValid($data)) {
			$obj->setEmail($form->getValue('email'));

			$obj->setFirstname($form->getValue('firstname'));
			$obj->setLastname($form->getValue('lastname'));
			$obj->setRole($form->getValue('role'));
			if ($form->getValue('password') != null || $form->getValue('password') != "") {
				$obj->setPassword($form->getValue('password'));
			}
			try {
				$this->_em->persist($obj);
				$this->_em->flush();
				$this->_message('update_success');
				return true;
			} catch (\exception $e) {
				echo "Unable to update " . $e->getMessage();
				return false;
			}
		}
	}

	/**
	 *
	 * @param integer $id
	 * @param array $data
	 * @throws \exception
	 * @return boolean
	 */
	public function acl_update($id, array $data) {
		$form = $this->getForm();
		$form->setSubmitLabel("Update");
		$obj = $this->_repository->find($id);

		if ($data['email'] != $obj->getEmail()) {
			$form->getElement('email')->addValidator(new \App\Validate\User\Email());
		}
		if (is_object($obj)) {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::NOT_FOUND);
		}
		if ($form->isValid($data)) {
			$obj->setEmail($form->getValue('email'));
			$obj->setFirstname($form->getValue('firstname'));
			$obj->setLastname($form->getValue('lastname'));
			try {
				$this->_em->persist($obj);
				$this->_em->flush();
				$this->_message('update_success');
				return true;
			} catch (\exception $e) {
				echo "Unable to create. " . $e->getMessage();
				return false;
			}
		}
	}

	public function acl_delete($id) {
		$obj = $this->_repository->find($id);
		if (is_object($obj)) {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::NOT_FOUND);
		}
		$this->_em->remove($obj);
		$this->_em->flush();
		return true;
	}

	public function activate($email, $token) {
		$user = $this->findByEmail($email);
		if (!is_object($user)) {
			throw new \exception(self::NOT_FOUND);
		}
		$correctToken = hash('sha512', $user->getId() . $user->getEmail() . $user->getUsername());
		if ($correctToken != $token) {
			return false;
		} else {
			$user->setActivated(true);
			$this->_em->persist($user);
			$this->_em->flush();
			return true;
		}
	}

	public function sendRegistrationEmail($userId, $options = array()) {
		$userObj = $this->_repository->find($userId);
		if (is_object($userObj)) {
		} else {
			throw new \exception(self::NOT_FOUND);
		}
		if (!array_key_exists('url', $options)) {
			$url = \Zend_Registry::getInstance()->get('system')->PUBLIC_SERVER_NAME;
			if (empty($url)) {
				$url = 'localhost';
			}
		} else {
			$url = $options['url'];
		}

		// Token hashing algorithm -- used for simple user activation / e-mail
		// validation
		$token = hash('sha512', $userObj->getId() . $userObj->getEmail() . $userObj->getUsername());
		// send an e-mail
		$notificationService = new \App\Service\Notification();
		$templateParams = array('email' => $userObj->getEmail(), 'firstname', $userObj->getFirstname(), 'token' => $token, 'url' => $url, 'user' => $userObj
		);
		$notificationService->sendIndividualEmail("userRegistered.phtml", "Please activate your account!", $userObj->getEmail(), $templateParams);

		return true;
	}

	public function findByEmail($email) {
		return $this->_repository->findOneByEmail($email);
	}

	public function findOneByUsername($username) {
		return $this->_repository->findOneByUsername($username);
	}

	public function find($id) {
		return $this->_repository->find($id);
	}

	public function findAll() {
		return $this->_repository->findAll();
	}

	public function getForm() {
		if (!isset($this->_form)) {
			$this->_form = new \App\Form\User();
		}
		return $this->_form;
	}

	public function getForgotPasswordForm() {
		if (!isset($this->_forgotPasswordForm)) {
			$this->_forgotPasswordForm = new \App\Form\User\ForgotPassword();
		}
		return $this->_forgotPasswordForm;
	}

	public function forgotPassword(array $data, $options = array()) {
		if (!array_key_exists('url', $options)) {
			$url = \Zend_Registry::getInstance()->get('system')->PUBLIC_SERVER_NAME;
			if (empty($url)) {
				$url = 'localhost';
			}
		} else {
			$url = $options['url'];
		}

		$form = $this->getForgotPasswordForm();
		if ($form->isValid($data)) {
			$user = $this->findByEmail($form->getValue('email'));
			if (!is_object($user)) {
				throw new \exception(self::NOT_FOUND);
			}
			$email = $form->getValue('email');
			$token = $token = hash('sha512', $user->getId() . $user->getEmail() . $user->getUsername() . $user->getPassword());
			$link = "https://$url/resetpassword/$email/$token";
			$this->_message('forgot_password_success');
			$mail = new \TP\Communication\Email();
			$mail->assignTemplateParam('link', $link);
			$mail->setSubject("Password Reset Request")->addTo($data['email']);
			$mail->sendHtmlTemplate('forgotPassword.phtml');
			return true;
		}
	}

	public function resetPassword(array $data) {
		$user = $this->findByEmail($data['email']);
		$form = $this->getResetPasswordForm();
		if ($form->isValid($data)) {
			if ($this->validateResetPasswordToken($user, $data['token'])) {
				$user->setPassword($form->getValue('password1'));
				$this->_em->persist($user);
				$this->_em->flush();
				$this->_message('password_changed');
				return true;
			} else {
				throw new \exception("App/Service/User - An invalid token was provided.");
			}
		}
		return false;
	}

	public function acl_getProfile($userId) {
		$obj = $this->_repository->find($userId);
		if (is_object($obj)) {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::NOT_FOUND);
		}
		$data = $obj->toArray();

		// User Profile Image

		$opt = array();
		if (file_exists(SHARE_PATH . DIRECTORY_SEPARATOR . "user" . DIRECTORY_SEPARATOR . "image" . DIRECTORY_SEPARATOR . $obj->getId() . ".png")) {

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
			$filename = "user/image/" . $obj->getId() . ".png";
			$cf = new \AmazonCloudFront();
			$distribution_hostname = \Zend_Registry::getInstance()->get('cloud')->aws->CLOUDFRONT_DOWNLOAD_DISTRIBUTION_URL;
			// Options should include restrictors such as IP Address,
			// Duration,
			// etc.
			$expires = strtotime('+' . $numberOfMinutes . 'minutes'); // time
			$url = $cf->get_private_object_url($distribution_hostname, $filename, $expires, $opt);
			$imgUrl = $url;
		} else {
			$imgUrl = "/img/icons/people-xbill.png";
		}

		$data['pic_url'] = $imgUrl;

		unset($data['password']);
		unset($data['role']);
		unset($data['email']);
		return $data;
	}

	public function acl_updateProfile(array $data) {
		if (!$this->isAllowed(null, __FUNCTION__)) {
			throw new \exception(self::PERMISSION_DENIED);
		}

		$obj = $this->_user;

		$form = new \App\Form\User();
		$form->removeElement('username');
		$form->removeElement('activated');
		$form->removeElement('role');
		$form->removeElement('password');
		$form->removeElement('email');

		// Image file

		$file = new \Zend_Form_Element_File('file');
		$file->setLabel('File')->setRequired(false)->setMaxFileSize('209715200')->addValidator('MimeType', false, array('image/png', 'image/jpeg', 'application/octet-stream'));

		$form->addElement($file);

		if ($form->isValid($data)) {
			$obj->setFirstname($form->getValue('firstname'));
			$obj->setLastname($form->getValue('lastname'));

			if ($form->file->isUploaded()) {

				$oldname = \pathinfo($form->file->getFileName());

				$newname = SHARE_PATH . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . "image" . DIRECTORY_SEPARATOR . $obj->getId() . '_tmp.' . $oldname['extension'];

				$finalname = SHARE_PATH . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . "image" . DIRECTORY_SEPARATOR . $obj->getId() . '.png';

				$form->file->addFilter('Rename', array('target' => $newname, 'overwrite' => true));

				$form->getValues();

				$form->file->getTransferAdapter()->setOptions(array('useByteString' => false));

				$form->convertImage($newname, $finalname);
				$form->uploadImage($finalname);
			}
			$this->_em->persist($obj);

			$this->_em->flush();
			$this->_user = $obj;

			return $obj;
		}
	}

	public function acl_adminResetPassword($id, array $data) {
		$obj = $this->_repository->find($id);
		if (is_object($obj)) {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::NOT_FOUND);
		}
		$form = $this->getResetPasswordForm();
		if ($form->isValid($data)) {
			$obj->setPassword($form->getValue('password1'));
			$this->_em->persist($obj);
			$this->_em->flush();
			$this->_message('password_changed');
			return true;
		} else {
			throw new \exception(self::FORM_INVALID);
		}
	}

	public function getResetPasswordForm() {
		if (!isset($this->_resetPasswordForm)) {
			$this->_resetPasswordForm = new \App\Form\User\ResetPassword();
		}
		return $this->_resetPasswordForm;
	}

	public function getDeleteForm() {
		if (!isset($this->_deleteForm)) {
			$this->_deleteForm = new \App\Form\User\Delete();
		}
		return $this->_deleteForm;
	}

	public function validateResetPasswordToken($user, $token) {
		if (is_object($user)) {
			$correctToken = hash('sha512', $user->getId() . $user->getEmail() . $user->getUsername() . $user->getPassword());
			if ($token == $correctToken) {
				return true;
			} else {
				return false;
			}
		} else {
			throw new \exception("App/Service/User/validateResetToken - userObj is not an object.");
		}
		return false;
	}
	/*
	 * (non-PHPdoc) @see \App\Service\iUser::acl_adminEnableUserAccount()
	 */
	public function acl_adminEnableUserAccount($userId) {
		// TODO Auto-generated method stub
	}

	/*
	 * (non-PHPdoc) @see \App\Service\iUser::acl_adminDisableUserAccount()
	 */
	public function acl_adminDisableUserAccount($userId) {
		// TODO Auto-generated method stub
	}
}
