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
namespace tests\application\services\Monitoring;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class EventTest extends \Zend_Test_PHPUnit_ControllerTestCase {

	protected $_service = null;

	public function setUp() {
		$this->_service = new \App\Service\Monitoring\Event();

	}

	public function testFind() {
		$this->assertInstanceOf('\App\Entity\Monitoring\Event', $this->_service->find(1));
	}

	public function testFindAll() {
		$this->assertInternalType('array', $this->_service->findAll());
	}
	public function testCreate() {
        
	}
	public function testUpdate() {

	}

	public function testDelete() {

	}
	public function testGetForm() {

	}
	public function testGetDeleteForm() {

	}

	public function testFindRecentEvents() {

	}

	public function tearDown() {
		$this->_em = \Zend_Registry::get('em');
		$this->_em->clear();
		unset($this->_service);
		unset($this->_userService);
	}
}
