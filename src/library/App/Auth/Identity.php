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
namespace App\Auth;

class Identity implements \Zend_Acl_Role_Interface {

	protected $_userId = null;

	public function __construct($userId) {
		$this->_userId = $userId;
	}

	public function getRoleId() {
		$service = new \App\Service\User();
		try {
			return $service->authorize($this->_userId);
		} catch (\exception $e) {
			if ($e->getMessage() == \App\Service\User::NOT_FOUND) {
				\Zend_Auth::getInstance()->clearIdentity();
				\Zend_Session::destroy(true);
				return \App\Acl\Roles::VISITOR;
			} else {
				throw new \exception($e->getMessage());
			}

		}
	}

	public function getUser() {
		$service = new \App\Service\User();
		$result = $service->find($this->_userId);
		return $result;
	}

	public function getCourseRoleId($courseId) {
		$service = new \App\Service\User();
		return $service->authorizeSubscription($this->_userId, $courseId);
	}
}
