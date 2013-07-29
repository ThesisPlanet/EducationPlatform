<?php

namespace TP\Controller\Plugin;

class SSL extends \Zend_Controller_Plugin_Abstract {
	public function preDispatch(\Zend_Controller_Request_Abstract $request) {
		$options = \Zend_Registry::getInstance ()->get ( 'sslConfig' );
		// Check config file to see if 'sslplugin.settings.active' exists
		if (isset ( $options->sslplugin->settings->active )) {
			$allowPlugin = $options->sslplugin->settings->active;
			// Check config to see if 'sslplugin.settings.active' is set to true
			if ($allowPlugin === "1") {
				$module = $request->module;
				$controller = $request->controller;
				$action = $request->action;
				$server = $request->getServer ();
				if (isset ( $server ['HTTP_HOST'] )) {
					$hostname = $server ['HTTP_HOST'];
				} else {
					$hostname = 'localhost';
				}
				$secureUrl = false;
				$routeRequest = false;
				
				// Check module
				if (isset ( $options->sslplugin->$module->require_ssl ))
					$secureUrl = ($options->sslplugin->$module->require_ssl) ? true : false;
					// Check Controller
				if (isset ( $options->sslplugin->$module->$controller->require_ssl ))
					$secureUrl = ($options->sslplugin->$module->$controller->require_ssl) ? true : false;
					// Check Action
				if (isset ( $options->sslplugin->$module->$controller->$action->require_ssl ))
					$secureUrl = ($options->sslplugin->$module->$controller->$action->require_ssl) ? true : false;
					// If the uri requires SSL, make sure its set to SSL
					// If its not supposed to be SSL, make sure its not
				if (($secureUrl & ! $request->isSecure ()) || (! $secureUrl & $request->isSecure ())) {
					// Set to http or https and create new url
					$httpScheme = ($request->isSecure ()) ? \Zend_Controller_Request_Http::SCHEME_HTTP : \Zend_Controller_Request_Http::SCHEME_HTTPS;
					$url = $httpScheme . "://" . $hostname . $request->getPathInfo ();
					// redirect to new url
					$redirector = \Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
					$redirector->setGoToUrl ( $url );
					$redirector->redirectAndExit ();
				}
			}
		}
	}
}