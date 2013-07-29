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
namespace App\Controller\Helper;

class ACL {

	protected $acl = null;

	/**
	 * This class will need to be optimized for performance later on.
	 * Currently
	 * the session object is rewritten on every request.
	 */
	public function __construct() {
		$this->acl = new \Zend_Acl();
	}

	public function setRoles() {
		$this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::VISITOR));
		$this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::USER), \App\Acl\Roles::VISITOR);
		$this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::SUBSCRIBER), \App\Acl\Roles::USER);
		$this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::PROVIDER), \App\Acl\Roles::SUBSCRIBER);
		$this->acl->addRole(new \Zend_Acl_Role(\App\Acl\Roles::ADMIN), \App\Acl\Roles::PROVIDER);
	}

	public function setResources() {
		// PUBLIC Resources
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::HELP));
		// USER (My Account)
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::MY_INDEX));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::MY_ACCOUNT));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::MY_PRIVACY));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::MY_NOTIFICATION));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::MY_PROFILE));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::MY_PASSWORD));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::MY_SUBSCRIPTION));

		// SITE Module

		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::SITE_ASSESSMENT));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::SITE_AUDIO));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::SITE_COURSE));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::SITE_ERROR));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::SITE_FILE));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::SITE_INDEX));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::SITE_VIDEO));

		// ADMIN
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::ADMIN_INDEX));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::ADMIN_MONITORING));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::ADMIN_USER));

		// Initialization
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::INITIALIZATION_INDEX));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::INITIALIZATION_AWS));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::INITIALIZATION_ZENCODER));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::INITIALIZATION_SERVER));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::INITIALIZATION_EMAIL));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::INITIALIZATION_ADMIN));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::INITIALIZATION_USER));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::INITIALIZATION_TEST));
		$this->acl->add(new \Zend_Acl_Resource(\App\Acl\Resources::INITIALIZATION_BACKUP));
	}

	public function setPrivileges() {
		// Current assumptions - everyone should have full access to all actions
		// within their grouped controllers.
		/*
		 * VISITORS
		 */
		$this->acl->allow(\App\Acl\Roles::VISITOR, \App\Acl\Resources::HELP, array('index'));

		$this->acl->allow(\App\Acl\Roles::VISITOR, \App\Acl\Resources::SITE_ERROR, array('index'));

		$this->acl->allow(\App\Acl\Roles::VISITOR, \App\Acl\Resources::SITE_INDEX, array('index'));

		/*
		 * USERS
		 */
		$this->acl->allow(\App\Acl\Roles::USER, \App\Acl\Resources::SITE_AUDIO, array('index', 'view'));

		$this->acl->allow(\App\Acl\Roles::USER, \App\Acl\Resources::SITE_FILE, array('index', 'view'));
		$this->acl->allow(\App\Acl\Roles::USER, \App\Acl\Resources::SITE_COURSE, array('index', 'view', 'popular', 'topic'));
		$this->acl->allow(\App\Acl\Roles::USER, \App\Acl\Resources::SITE_VIDEO, array('index', 'view'));
		$this->acl->allow(\App\Acl\Roles::USER, \App\Acl\Resources::MY_INDEX, array('index'));
		$this->acl->allow(\App\Acl\Roles::USER, \App\Acl\Resources::MY_ACCOUNT, array('index'));
		$this->acl->allow(\App\Acl\Roles::USER, \App\Acl\Resources::MY_PASSWORD, array('index'));
		$this->acl->allow(\App\Acl\Roles::USER, \App\Acl\Resources::MY_PROFILE, array('index'));

		/*
		 * SUBSCRIBERS
		 */
		$this->acl->allow(\App\Acl\Roles::SUBSCRIBER, \App\Acl\Resources::SITE_ASSESSMENT, array('index', 'view', 'take', 'viewquestion', 'viewresults'));

		/*
		 * PROVIDERS
		 */
		$this->acl->allow(\App\Acl\Roles::PROVIDER, \App\Acl\Resources::SITE_ASSESSMENT, array('add', 'edit', 'delete', 'addtf', 'addmc', 'addfib', 'deletequestion', 'editquestion', 'viewstudentscores'));
		$this->acl->allow(\App\Acl\Roles::PROVIDER, \App\Acl\Resources::SITE_COURSE, array('curriculum', 'delete', 'create'));
		/*
		 * ADMINISTRATOR
		 */
		$this->acl->allow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::ADMIN_INDEX, array('index'));
		$this->acl->allow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::ADMIN_MONITORING, array('index'));
		$this->acl->allow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::ADMIN_USER, array('index', 'edit', 'delete'));

		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'fully_managed.lock') == true) {
			// INITIALIZATION

			$this->acl->removeAllow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_INDEX, array('index'));
			$this->acl->removeAllow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_AWS, array('index'));
			$this->acl->removeAllow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_EMAIL, array('index'));
			$this->acl->removeAllow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_ADMIN, array('index'));
			$this->acl->removeAllow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_USER, array('index'));
			$this->acl->removeAllow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_SERVER, array('index', 'ssl', 'picture'));
			$this->acl->removeAllow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_ZENCODER, array('index'));
			$this->acl->removeAllow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_TEST, array('index'));
			$this->acl->removeAllow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_BACKUP, array('index'));
		} else {
			$this->acl->allow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_INDEX, array('index'));
			$this->acl->allow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_AWS, array('index'));
			$this->acl->allow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_EMAIL, array('index'));
			$this->acl->allow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_ADMIN, array('index'));
			$this->acl->allow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_USER, array('index'));
			$this->acl->allow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_SERVER, array('index', 'ssl', 'picture'));
			$this->acl->allow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_ZENCODER, array('index'));
			$this->acl->allow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_TEST, array('index'));
			$this->acl->allow(\App\Acl\Roles::ADMIN, \App\Acl\Resources::INITIALIZATION_BACKUP, array('index'));
		}
	}

	public function setAcl() {
		\Zend_Registry::set('Zend_Acl', $this->acl);
	}
}
