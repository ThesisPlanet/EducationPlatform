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
namespace App\Log\Writer;

class DB extends \Zend_Log_Writer_Abstract {
	/**
	 * array of log events
	 *
	 * @var array
	 */
	public $events = array ();
	/**
	 * shutdown called?
	 *
	 * @var boolean
	 */
	public $shutdown = false;
	/**
	 * Write a message to the log.
	 *
	 * @param $event array
	 *        	event data
	 * @return void
	 */
	public function _write($event) {
		$service = new \App\Service\Monitoring\Event ();
		$data = array ();
		$data = $event;
		
		if (\Zend_Registry::isRegistered ( 'sysem' )) {
			$data ['deviceIp'] = \Zend_Registry::get ( 'system' )->PRIVATE_IP;
			$data ['deviceName'] = \Zend_Registry::get ( 'system' )->HOSTNAME;
		} else {
			$data ['deviceIp'] = "127.0.0.1";
			$data ['deviceName'] = "localhost";
		}
		$service->create ( $data );
		return true;
	}
	/**
	 * Record shutdown
	 *
	 * @return void
	 */
	public function shutdown() {
		$this->shutdown = true;
	}
	/**
	 * Create a new instance of Zend_Log_Writer_Mock
	 *
	 * @param $config array|Zend_Config        	
	 * @return Zend_Log_Writer_Mock
	 */
	static public function factory($config) {
		return new self ();
	}
}