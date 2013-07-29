<?php

class Site_IndexController extends Zend_Controller_Action {

	public function init() {
	}

	public function indexAction() {
		if (\Zend_Auth::getInstance()->hasIdentity()) {
			$this->view->page = "My courses";
			$courseService = new \App\Service\Course();
			$courseService->setUser(\Zend_Auth::getInstance()->getIdentity()->getUser());
			$this->view->courseService = $courseService;

			$subscriptionList = $courseService->acl_findSubscriptions();

			if ($courseService->isAllowed(null, 'acl_create')) {
				$this->view->canCreateCourses = true;
			} else {
				$this->view->canCreateCourses = false;
			}
			$this->view->subscriptions = $subscriptionList;
			$this->view->service = $courseService;
		} else {
			$this->_redirect('/login');
		}
	}

	public function footerAction() {
	}
}
