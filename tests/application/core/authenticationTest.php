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
namespace tests\application\core;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class authenticationTest extends \Zend_Test_PHPUnit_ControllerTestCase {
	public function testValidAuthentication() {
		$cs = new \App\Service\Course ();
		$course = $cs->find ( 1 );
		\Zend_Registry::getInstance ()->set ( 'course', $course );
		$_SERVER ['HTTP_HOST'] = "localhost";
		$adapter = new \App\Auth\Adapter ( "jack.peterson@gmail.com", "456", $course );
		$this->assertInstanceOf ( "App\\Auth\\Adapter", $adapter );
		$adapter->authenticate ();
	}
	public function testInValidPasswordAuthentication() {
		$cs = new \App\Service\Course ();
		$course = $cs->find ( 1 );
		\Zend_Registry::getInstance ()->set ( 'course', $course );
		$_SERVER ['HTTP_HOST'] = "localhost";
		$adapter = new \App\Auth\Adapter ( "jack.peterson@gmail.com", "123", $course );
		$this->assertInstanceOf ( "App\\Auth\\Adapter", $adapter );
		$adapter->authenticate ();
	}
	public function testNonExistingUserAuthentication() {
		$cs = new \App\Service\Course ();
		$course = $cs->find ( 1 );
		\Zend_Registry::getInstance ()->set ( 'course', $course );
		$_SERVER ['HTTP_HOST'] = "localhost";
		$adapter = new \App\Auth\Adapter ( "nobody@nowhere.com", "123", $course );
		$this->assertInstanceOf ( "App\\Auth\\Adapter", $adapter );
		$adapter->authenticate ();
	}
	public function testNoSubscriptionAuthentication() {
		$cs = new \App\Service\Course ();
		$course = $cs->find ( 2 );
		\Zend_Registry::getInstance ()->set ( 'course', $course );
		$_SERVER ['HTTP_HOST'] = "localhost";
		$adapter = new \App\Auth\Adapter ( "jack.peterson@gmail.com", "456", $course );
		$this->assertInstanceOf ( "App\\Auth\\Adapter", $adapter );
		$adapter->authenticate ();
	}
}