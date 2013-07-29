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
class loggerTest extends \PHPUnit_Framework_TestCase
{
    protected $application;
    public function setUp ()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }
    public function appBootstrap ()
    {
        $this->application = new \Zend_Application(APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini');
        $this->application->bootstrap();
    }
    public function testLogger ()
    {
        $logger = \Zend_Registry::getInstance()->get('logger');
        $this->assertNull(
        $logger->log("testing logger via PHPUnit.", \Zend_Log::INFO));
    }
}