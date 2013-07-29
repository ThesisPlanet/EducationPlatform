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
namespace TP\Communication;

class Email extends \Zend_Mail {

	protected $_view;

	protected $_options;

	protected $_transport;

	protected $_protocol;

	protected $_server;

	static $_defaultView;

	public function __construct($charset = 'iso-8859-1', $server, $options = null) {
		parent::__construct($charset);
		$this->_view = self::getDefaultView();
		$this->_options = $options;
		$this->_server = $server;
		// Load up the SMTP transport and set protocol as a private variable.
	}

	protected function redoTransport() {
		try {
			unset($this->_transport);
		} catch (\exception $e) {
		}

		$this->_transport = new \Zend_Mail_Transport_Smtp($this->_server, $this->_options);
		return true;
	}

	protected static function getDefaultview() {
		if (null === self::$_defaultView) {
			self::$_defaultView = new \Zend_View();
			self::$_defaultView->setScriptPath(APPLICATION_PATH . '/layouts/communication/email');
		}
		return self::$_defaultView;
	}

	public function sendHtmlTemplate($template, $encoding = \Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
		$html = $this->_view->render($template);
		$this->setBodyHtml($html, $this->getCharset(), $encoding);
		// reset the connection -- assume that it timed out and needs to be
		// rebuilt.
		if (!$this->redoTransport())
			throw new \exception("Unable to reset email transport.");
		$this->send($this->_transport);

		if ($this->_transport instanceof \Zend_Mail_Transport_Abstract) {
			try {
				unset($this->_transport);
			} catch (\exception $e) {
			}
		}
		return $this;
	}

	public function assignTemplateParam($name, $value) {
		$this->_view->$name = $value;
		return $this;
	}

	public function getView($template) {
		return $this->_view->render($template);
	}
}
