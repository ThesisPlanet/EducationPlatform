<?php
namespace App\Service;

class Notification extends Base {

	private $_errors;

	const TEMPLATE_NOT_FOUND = "Unable to find that template file.";

	public function __construct() {
		$this->_errors = array();

	}

	public function __destruct() {
	}

	/**
	 * Sends email messages to each subscriber that accepts email notifications.
	 *
	 * @param string $templateName
	 * @param string $emailSubject
	 * @param integer $courseId
	 * @param array $templateParameters
	 * @return boolean
	 */
	public function system_sendBulkNotification($templateName, $emailSubject, $courseId, $templateParameters) {
		if (!file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'communication' . DIRECTORY_SEPARATOR . 'email' . DIRECTORY_SEPARATOR . $templateName)) {
			throw new \exception(self::TEMPLATE_NOT_FOUND);
		}

		$cs = new \App\Service\Course();
		$courseObj = $cs->find($courseId);
		if (!is_object($courseObj)) {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		$subscriptions = $courseObj->getSubscriptions();

		foreach ($subscriptions as $key => $subscriptionObj) {
			$userObj = $subscriptionObj->getUser();
			$this->sendIndividualEmail($templateName, $emailSubject, $userObj->getEmail(), $templateParameters);
		}
		return true;
	}

	/**
	 * Sends an email.
	 * Will save any exceptions thrown into $this->_errors.
	 *
	 * @param string $templateName
	 * @param string $emailSubject
	 * @param string $emailAddress
	 * @param array $templateParametersArray
	 * @return boolean
	 */
	public function sendIndividualEmail($templateName, $emailSubject, $emailAddress, $templateParametersArray) {
		$options = array('auth' => \Zend_Registry::getInstance()->get('communication')->email->smtp->auth, 'username' => \Zend_Registry::getInstance()->get('communication')->email->smtp->username, 'password' => \Zend_Registry::getInstance()->get('communication')->email->smtp->password,
				'ssl' => \Zend_Registry::getInstance()->get('communication')->email->smtp->ssl, 'port' => \Zend_Registry::getInstance()->get('communication')->email->smtp->port);

		$server = \Zend_Registry::getInstance()->get('communication')->email->smtp->server;
		$mail = new \TP\Communication\Email(null, $server, $options);
		if (is_array($templateParametersArray)) {
			foreach ($templateParametersArray as $key => $value) {
				$mail->assignTemplateParam($key, $value);
			}
		}
		$mail->addTo($emailAddress)->setSubject(htmlspecialchars($emailSubject));
		try {
			$mail->sendHtmlTemplate($templateName);
		} catch (\exception $e) {
			$logger = \Zend_Registry::getInstance()->get('logger');
			$logger->log($e->getMessage() . "\nTrace: \n" . $e->getTraceAsString(), \Zend_Log::ERR);
		}
		unset($mail);
		return true;
	}
}
