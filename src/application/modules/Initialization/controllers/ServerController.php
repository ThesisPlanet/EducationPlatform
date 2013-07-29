<?php

class Initialization_ServerController extends Zend_Controller_Action {

	public function init() {
		$this->_service = new \App\Service\Configuration();
		if (\Zend_Auth::getInstance()->hasIdentity()) {
			$this->_service->setUser(\Zend_Auth::getInstance()->getIdentity()->getUser());
		}
		if (\Zend_Auth::getInstance()->hasIdentity()) {
			$this->_service->setUser(\Zend_Auth::getInstance()->getIdentity()->getUser());
		}
		$this->view->service = $this->_service;
	}

	public function indexAction() {
		$configFile = CONFIGURATION_PATH . '/system.ini';
		if (!file_exists($configFile)) {
			$handle = fopen($configFile, "w+");
			$heading = "[" . APPLICATION_ENV . "]\n";
			fwrite($handle, $heading, strlen($heading));
			fclose($handle);
		}

		$config = new \Zend_Config_Ini($configFile, APPLICATION_ENV, array('allowModifications' => true));

		$form = new \App\Form\Configuration\Server();

		$form->populate(array('domain' => $config->PUBLIC_SERVER_NAME));

		if ($this->_request->isPost()) {
			if ($form->isValid($this->_request->getParams())) {
				$config->PUBLIC_SERVER_NAME = $form->getValue('domain');
				$config->PUBLIC_IP = gethostbyname($form->getValue('domain'));
				$config->HOSTNAME = gethostname();

				$writer = new \Zend_Config_Writer_Ini();
				$writer->setConfig($config);
				$writer->write(CONFIGURATION_PATH . '/system.ini', $config);

				$this->_redirect('/Initialization/');
			}
		}
		$this->view->form = $form;
	}

	public function pictureAction() {
		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'cloud.ini')) {
			$config = new \Zend_Config_Ini(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'cloud.ini', APPLICATION_ENV);
			if (isset($config->provider)) {
				$status['aws'] = "OK";
			} else {
				$this->_redirect("/Initialization/aws");
			}
		} else {
			$this->_redirect("/Initialization/aws");
		}

		$this->view->page = "Update header image";
		if (!\Zend_Registry::getInstance()->isRegistered('cloud')) {
			$this->_redirect('/Initialization/aws');
		}

		$form = new \App\Form\Configuration\UpdateImage();

		if ($this->_request->isPost()) {
			if ($form->isValid($this->_request->getParams())) {
				$this->_service->acl_updateImage();
				$this->_redirect('/Initialization/');
			}
		}

		$this->view->form = $form;
	}

	public function sslAction() {
		$this->view->page = "Update SSL Certificate";

		$form = new \App\Form\Configuration\SSL();

		if ($this->_request->isPost()) {
			if ($form->isValid($this->_request->getParams())) {

				// Load in the strings

				$privateKeyString = file_get_contents($_FILES['privateKey']['tmp_name']);
				$certificateString = file_get_contents($_FILES['publicKey']['tmp_name']);

				if (array_key_exists('bundle', $_FILES)) {

					$bundleString = file_get_contents($_FILES['bundle']['tmp_name']);

					$certificateString = $certificateString . $bundleString;
				}

				if ($this->_service->acl_updateSSLCertificates($certificateString, $privateKeyString))
					$this->_redirect('/Initialization/');
			}
		}

		$this->view->form = $form;
	}
}

