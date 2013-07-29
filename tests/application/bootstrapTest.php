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
namespace tests\application;
/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class bootstrapTest extends \PHPUnit_Framework_TestCase
{
    public $application;
    public function setUp ()
    {
        $this->bootstrap = new \Zend_Application(APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini');
        $this->bootstrap->bootstrap();
        parent::setUp();
    }
    public function tearDown ()
    {
        /*
         * Tear Down Routine
         */
    }
    public function testSSL ()
    {}
    public function testACL ()
    {}
    public function testUser ()
    {
        $service = new \App\Service\User();
        $u = $service->find(1);
        $this->assertInstanceOf("\\App\\Entity\\User", $u);
        $this->assertEquals(1, $u->getId());
        $this->assertInternalType("string", $u->getFirstname());
        $this->assertInternalType("string", $u->getLastname());
        $this->assertInternalType("array", $u->toArray());
    }
}