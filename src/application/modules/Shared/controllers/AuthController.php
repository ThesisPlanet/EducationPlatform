<?php

class Shared_AuthController extends Zend_Controller_Action {

	public function init() {

		/*
		 * Initialize action controller here
		 */
		Zend_Dojo::enableView($this->view);
	}

	public function loginAction() {
		$users = \Zend_Registry::getInstance()->get('users');
		if (isset($users->canRegister)) {
			if ($users->canRegister == true) {
				$this->view->displayRegister = true;
			} else {
				$this->view->displayRegister = false;
			}
		} else {
			$this->view->displayRegister = false;
		}

		$this->view->page = "Login";
		//$this->view->dojo()->enable();
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$this->_redirect("/"); // Send the user to the index to prevent
			// any
			// kind of redirect loop
		}
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		// action body
		$form = new \App\Form\Auth\Login();
		$request = $this->getRequest();
		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				$adapter = new \App\Auth\Adapter($this->_getParam('email'), $this->_getParam('password'));
				$result = Zend_Auth::getInstance()->authenticate($adapter);
				if (Zend_Auth::getInstance()->hasIdentity()) {
					Zend_Auth::getInstance()->getStorage()->write($result->getIdentity());
					$session = new Zend_Session_Namespace('app.auth');
					if (isset($session->requestURL) and $session->requestURL != null) {
						$url = $session->requestURL;
						$this->_redirect($url);
					} else {
						$this->_redirect('/');
					}
				} else {
					$this->view->message = implode(' ', $result->getMessages());
				}
			}
		}
		$this->view->form = $form;
	}

	public function logoutAction() {
		Zend_Auth::getInstance()->clearIdentity();
		\Zend_Session::destroy(true);
		$this->_redirect('/login'); // back to login page
	}

	public function forgotpasswordAction() {
		$service = new \App\Service\User();
		if ($this->_request->isPost()) {
			try {
				$response = $service->forgotPassword($this->_request->getPost());
				if ($response == true) {
					$this->_helper->flashMessenger($service->getMessage());
					$this->_redirect('/login');
				}
			} catch (\exception $e) {
				switch ($e->getMessage()) {
				case 'not_found':
					$this->view->messages = array('No account was found to be associated with that e-mail address.');
					break;
				default:
					$this->view->messages = array($e->getMessage());
				}
			}
		}
		$form = $service->getForgotPasswordForm();
		$this->view->form = $form;
	}

	public function resetpasswordAction() {
		$service = new \App\Service\User();
		$user = $service->findByEmail($this->_request->getParam('email'));
		if (!is_object($user)) {
			$this->view->messages = array("No user found with that e-mail address.");
		} else {
			if ($service->validateResetPasswordToken($user, $this->_request->getParam('token')) == true) {
				$form = $service->getResetPasswordForm();
				if ($this->_request->isPost()) {
					$result = $service->resetPassword($this->_request->getParams());
					if ($result) {
					}
					if ($result == true) {
						$this->_redirect('/login');
					} else {
					}
					$this->_helper->flashMessenger($service->getMessage());
				}
				$this->view->messages = $this->_helper->flashMessenger->getMessages();
				$this->view->form = $form;
			} else {
				echo "An invalid token was provided.";
			}
		}
	}

	public function cookieAction() {
		setcookie('form-123', base64_encode($_SERVER['HTTP_REFERER']), time() + 3600);
		echo $_COOKIE['form-123'];
		$this->_helper->viewRenderer->setNoRender(true);
	}
}

