<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	protected function _initDefaultHelpers() {
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->headTitle('Education Platform');
		$view->headTitle()->setSeparator(' :: ');
	}
	public function _initConfig() {
		Zend_Registry::set('config', $this->getOptions());
	}
	public function _initAutoloaderNamespaces() {
		if (APPLICATION_ENV == "development") {
			require_once 'E:/php_include/doctrine-2.3.2/Doctrine/Common/ClassLoader.php';
		} else {
			require_once '/usr/share/composer/DoctrineORM/vendor/doctrine/common/lib/Doctrine/Common/ClassLoader.php';
		}
		$autoloader = \Zend_Loader_Autoloader::getInstance();
		$fmmAutoloader = new \Doctrine\Common\ClassLoader('Bisna');
		$autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'Bisna');
	}
	public function _initDoctrineConfiguration() {
		$this->bootstrap('Doctrine');
		\Zend_Registry::getInstance()->set('em', $this->getResource('doctrine')->getEntityManager());
		$connection = \Zend_Registry::getInstance()->get('em')->getConnection()->getWrappedConnection();
		$connection->setAttribute(PDO::MYSQL_ATTR_LOCAL_INFILE, true);
		$connection->query("select 1");
	}
	public function _initRoutes() {
		$front = Zend_Controller_Front::getInstance();
		$router = $front->getRouter();
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'production');
		$router->addConfig($config, 'routes');
	}
	public function _initSSL() {
		$front = Zend_Controller_Front::getInstance();
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/ssl.ini', APPLICATION_ENV);
		Zend_Registry::getInstance()->set('sslConfig', $config);
		$front->registerPlugin(new \TP\Controller\Plugin\SSL());
	}
	public function _initACL() {
		$this->bootstrap('ConfigurationPath');
		$this->bootstrap('uploadPath');
		$this->bootstrap('SystemParamsAndLogger');
		$front = Zend_Controller_Front::getInstance();
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/acl.ini', APPLICATION_ENV);
		Zend_Registry::getInstance()->set('aclConfig', $config);
		$helper = new \App\Controller\Helper\ACL();
		$helper->setRoles();
		$helper->setResources();
		$helper->setPrivileges();
		$helper->setAcl();
		$front->registerPlugin(new \TP\Controller\Plugin\ACL());
	}
	public function _initTopNavigation() {
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
		$navigation = new Zend_Navigation($config);
		$view->navigation($navigation);
	}

	public function _initDojo() {
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');

		$view = $layout->getView();
		\Zend_Dojo::enableView($view);
		$view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
		$view->dojo()->disable();
	}
	public function _initCustomDoctrineTypes() {
		$this->bootstrap('Doctrine');
		\Zend_Registry::getInstance()->set('em', $this->getResource('doctrine')->getEntityManager());
	}
	protected function _initCloud() {
		$this->bootstrap('ConfigurationPath');
		$this->bootstrap('uploadPath');
		$this->bootstrap('SystemParamsAndLogger');
		$front = Zend_Controller_Front::getInstance();
		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "cloud.ini")) {
			$config = new Zend_Config_Ini(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'cloud.ini', APPLICATION_ENV);
			Zend_Registry::getInstance()->set('cloud', $config);
			require_once 'Aws/sdk.class.php';
			require_once 'Aws/class.cloudFrontNinja.php';
		} else {
		}
	}
	protected function _initQueue() {
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/queue.ini', APPLICATION_ENV);
		$queue = new \App\Service\Core\Queue($config);
		Zend_Registry::getInstance()->set('queue', $queue);
	}
	protected function _initEncoder() {
		$this->bootstrap('ConfigurationPath');

		$front = Zend_Controller_Front::getInstance();

		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "encoder.ini")) {
			$config = new Zend_Config_Ini(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'encoder.ini', APPLICATION_ENV);
			Zend_Registry::getInstance()->set('encoder', $config);
			$provider = Zend_Registry::getInstance()->get('encoder')->provider;
			if (isset($provider) and $provider != null) {
				switch ($provider) {
				case 'zencoder':
					require_once 'vendor/Zencoder.php';
					break;
				default:
					throw new \exception("An invalid encoding provider was specified.");
				}
			} else {
				throw new \exception("encoding provider not specified! Please edit the configs/encoder.ini file. What was specified was:" . Zend_Registry::getInstance()->get('encoder')->provider);
			}
		} else {
			// DONT BOOTSTRAP ENCODER
		}
	}
	protected function _initUploadPath() {
		if (is_dir(realpath(dirname(__FILE__) . '/../../shared'))) {
			defined('SHARE_PATH') || define('SHARE_PATH', realpath(dirname(__FILE__) . '/../../shared'));
		} else {
			if (is_dir(realpath(dirname(__FILE__) . '/../../../../shared'))) {
				defined('SHARE_PATH') || define('SHARE_PATH', realpath(dirname(__FILE__) . '/../../../../shared'));
			} else {
				defined('SHARE_PATH') || define('SHARE_PATH', realpath(dirname(__FILE__) . '/../untrusted'));
			}
		}
	}
	protected function _initConfigurationPath() {
		// Deployed via RPM
		if (is_dir(realpath(dirname(__FILE__) . '/../../configuration'))) {
			defined('CONFIGURATION_PATH') || define('CONFIGURATION_PATH', realpath(dirname(__FILE__) . '/../../configuration'));
		} else {
			// Not deployed via RPM
			if (is_dir(realpath(dirname(__FILE__) . '/../../../../configuration'))) {
				defined('CONFIGURATION_PATH') || define('CONFIGURATION_PATH', realpath(dirname(__FILE__) . '/../../../../configuration'));
			} else {
				// for testing purposes
				defined('CONFIGURATION_PATH') || define('CONFIGURATION_PATH', realpath(dirname(__FILE__) . '/../configuration'));
			}
		}
	}
	protected function _initBackupPath() {
		if (is_dir(realpath(dirname(__FILE__) . '/../../backup'))) {
			defined('BACKUP_PATH') || define('BACKUP_PATH', realpath(dirname(__FILE__) . '/../../backup'));
		} else {
			if (is_dir(realpath(dirname(__FILE__) . '/../../../../backup'))) {
				defined('BACKUP_PATH') || define('BACKUP_PATH', realpath(dirname(__FILE__) . '/../../../../backup'));
			} else {
				defined('BACKUP_PATH') || define('BACKUP_PATH', realpath(dirname(__FILE__) . '/../untrusted'));
			}
		}
	}
	protected function _initSystemParamsAndLogger() {
		$this->bootstrap('ConfigurationPath');

		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "system.ini")) {
			$config = new Zend_Config_Ini(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'system.ini', APPLICATION_ENV);
			Zend_Registry::getInstance()->set('system', $config);
		} else {
			// DONT BOOTSTRAP SYSTEM INFO
		}
		$writer = new \App\Log\Writer\DB();
		$logger = new Zend_Log($writer);
		Zend_Registry::getInstance()->set('logger', $logger);
		Zend_Registry::set('Zend_Log', $logger);
	}
	protected function _initCommunication() {
		$this->bootstrap('ConfigurationPath');
		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "communication.ini")) {
			$config = new Zend_Config_Ini(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "communication.ini", APPLICATION_ENV);
			Zend_Registry::getInstance()->set('communication', $config);
		} else {
			// DONT BOOTSTRAP COMMUNICATION
		}
	}
	protected function _initUserSettings() {
		$this->bootstrap('ConfigurationPath');
		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "users.ini")) {
			$config = new Zend_Config_Ini(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . "users.ini", APPLICATION_ENV, true);
			Zend_Registry::getInstance()->set('users', $config);
		} else {
			Zend_Registry::getInstance()->set('users', new Zend_Config(array(), false));
		}
	}
	protected function _initConfigurationChecker() {
		if (file_exists(CONFIGURATION_PATH . DIRECTORY_SEPARATOR . 'configured.ini')) {
			Zend_Registry::getInstance()->set('configured', true);
		} else {
			$front = Zend_Controller_Front::getInstance();
			$front->registerPlugin(new \App\Controller\Plugin\ConfigurationChecker());
		}
	}
}
