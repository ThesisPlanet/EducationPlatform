<?php

class Site_CourseController extends Zend_Controller_Action {

	protected $_service;

	public function init() {
		$this->_service = new \App\Service\Course();
		$this->_assessmentService = new \App\Service\Course\Assessment();

		if (\Zend_Auth::getInstance()->hasIdentity()) {
			$this->_service->setUser(\Zend_Auth::getInstance()->getIdentity()->getUser());
			$this->_assessmentService->setUser(\Zend_Auth::getInstance()->getIdentity()->getUser());
		}
		$this->view->service = $this->_service;
	}

	public function indexAction() {
		$this->view->page = "Browse Courses";
		$courseList = $this->_service->findMostRecent();
		$this->view->list = $courseList;
		$this->view->service = $this->_service;
	}

	public function topicAction() {
		$this->view->page = "Courses in: " . $this->view->escape($this->_request->getParam('topic'));
		if (null !== $this->_request->getParam('topic')) {
			$courseList = $this->_service->findByTopic($this->_request->getParam('topic'));
			$this->view->list = $courseList;
		} else {
			$this->view->topicList = $this->_service->findAllEnabledTopics();
		}

		$this->view->topic = $this->_request->getParam('topic');
	}

	public function popularAction() {
		$this->view->page = "Most popular courses";
		$courseList = $this->_service->findMostPopular();
		$this->view->list = $courseList;
	}

	public function permissionsAction() {
		$this->view->current = "permissions";

		if (null !== $this->_request->getParam('id')) {
			$course = $this->_service->find($this->_request->getParam('id'));
			if (!is_object($course)) {
				$course = $this->_service->findOneByTitle($this->_request->getParam('id'));
			}
		}
		if (!is_object($course)) {
			$this->view->page = "Course not found";
			$this->_response->setHttpResponseCode(404);
			$this->view->message = "Unable to find that course.";
		} else {
			$this->view->course = $course;
		}
	}

	public function viewAction() {
		if (null !== $this->_request->getParam('id')) {
			$course = $this->_service->find($this->_request->getParam('id'));
			if (!is_object($course)) {
				$course = $this->_service->findOneByTitle($this->_request->getParam('id'));
			}
		}
		if (!is_object($course)) {
			$this->view->page = "Course not found";
			$this->_response->setHttpResponseCode(404);
			$this->view->message = "Unable to find that course.";
		} else {
			$this->view->page = $course->getTitle();
			$this->view->course = $course;
			$this->view->content = $course->getContent();
			$this->view->completedContent = array();
			$completedAssessments = array();

			$sortedContent = array();

			// Chapters
			$chapters = $course->getChapters();
			if ($this->_service->isAllowed($course, 'acl_listContentOrder'))
				$this->view->sortedContent = $this->_service->acl_listContentOrder($course->getId());
			if ($this->_service->isAllowed($course, 'acl_getPercentComplete'))
				$this->view->percentComplete = $this->_service->acl_getPercentComplete($course->getId());
			if (\Zend_Auth::getInstance()->hasIdentity()) {
				$subscriptions = $this->_service->acl_findSubscriptions();
				if (is_array($subscriptions)) {
					foreach ($subscriptions as $key => $subscriptionObj) {
						if ($subscriptionObj->getCourse()->getId() == $course->getId()) {
							$this->view->page = $course->getTitle();
							$this->_helper->viewRenderer->setRender('view-subscriber');
							$this->view->completedContent = $subscriptionObj->getCompletedContent();

							foreach ($course->getAssessments() as $assessmentObj) {
								$attempts = $this->_assessmentService->acl_listAttempts($assessmentObj->getId());
								foreach ($attempts as $attemptKey => $attemptObj) {
									if ($attemptObj->getFinishedAt() instanceof \DateTime) {
										$assessmentCompleted = true;
									}
								}
								if ($assessmentCompleted == true) {
									$completedAssessments[$assessmentObj->getId()] = true;
								}
							}
							$this->view->completedAssessments = $completedAssessments;
							break;
						}
					}
				}
			}
		}
	}

	public function createAction() {
		$this->view->page = "Create a new course";
		$form = $this->_service->getForm();
		if ($this->_request->isPost()) {
			if ($form->isValid($this->_request->getParams())) {
				try {
					$courseId = $this->_service->acl_create($form->getValues());
					$this->_redirect('/course/curriculum/' . $courseId);
				} catch (\exception $e) {
					$this->view->message = $e->getMessage();
				}
			}
		}
		$this->view->form = $form;
	}

	public function deleteAction() {
		$this->view->page = "Delete course";
		$form = $this->_service->getDeleteForm();
		if ($this->_request->isPost()) {
			if ($form->isValid($this->_request->getParams())) {
				try {
					$result = $this->_service->acl_delete($this->_request->getParam('id'));
					if ($result) {
						$this->_redirect('/');
					}
				} catch (\exception $e) {
					$this->view->message = $e->getMessage();
				}
			}
		}
		$this->view->form = $form;
	}

	public function editAction() {
	}

	public function subscribeAction() {
		if (null !== $this->_request->getParam('id')) {
			$course = $this->_service->find($this->_request->getParam('id'));
		}
		if (null !== $this->_request->getParam('title')) {
			$course = $this->_service->findByTitle($this->_request->getParam('id'));
		}
		try {
			$this->_service->acl_subscribe($course->getId());
			$this->_redirect('/course/view/' . $course->getId());
		} catch (\exception $e) {
			$this->view->message = $e->getMessage();
		}
	}

	/**
	 * Manage subscribers
	 */
	public function subscribersAction() {
		$this->view->current = "subscribers";
		if (null !== $this->_request->getParam('id')) {
			$course = $this->_service->find($this->_request->getParam('id'));
			if (!is_object($course)) {
				$course = $this->_service->findOneByTitle($this->_request->getParam('id'));
			}
		}
		if (!is_object($course)) {
			$this->view->page = "Course not found";
			$this->_response->setHttpResponseCode(404);
			$this->view->message = "Unable to find that course.";
		} else {
			$this->view->page = $course->getTitle();
			$this->view->course = $course;
			$this->view->content = $course->getContent();
		}
	}

	public function editsubscriptionAction() {
		$subscription = $this->_service->acl_providerFindSubscription($this->_request->getParam('id'));
		$form = new \App\Form\Subscription();
		$form->removeElement('user');
		$form->removeElement('course');
		$role = $form->getElement('role');
		$role->removeMultiOption('visitor');
		$role->removeMultiOption('user');
		$form->populate($subscription->toArray());
		if ($this->_request->isPost()) {

			if ($form->isValid($this->_request->getParams())) {
				if ($this->_service->acl_providerUpdateSubscriptionRole($this->_request->getParam('id'), $form->getValue('role')) == true) {
					$this->_redirect('/course/subscribers/' . $subscription->getCourse()->getId());
				}
			} else {
				throw new \exception("Form invalid");
			}
		}
		$this->view->subscription = $subscription;
		$this->view->form = $form;
	}
	// TODO: FINISH ME
	public function deletesubscriptionAction() {
		$subscription = $this->_service->acl_providerFindSubscription($this->_request->getParam('id'));
		$form = new \App\Form\SubscriptionDelete();
		$form->populate($subscription->toArray());
		if ($this->_request->isPost()) {
			if ($form->isValid($this->_request->getParams())) {
				if ($this->_service->acl_providerDeleteSubscription($form->getValue('id')) == true) {
					$this->_redirect('/course/subscribers/' . $subscription->getCourse()->getId());
				}
			} else {
				throw new \exception("Form invalid");
			}
		}
		$this->view->subscription = $subscription;
		$this->view->form = $form;
	}

	public function notfoundAction() {
	}

	public function removesubscriberAction() {
		// receive an email address + courseId.
		// TODO: Write me :-)
	}

	public function updatesubscriberAction() {
		// modify the role for a subscription
		// TODO: Write me.
	}

	public function inviteAction() {
		$form = new \App\Form\Course\InviteUser();
		$form->getElement('email')->setLabel("Please provide an email address");
		$form->getElement('email')->addValidator(new \App\Validate\User\EmailExists());
		if ($this->_request->isPost()) {
			if ($form->isValid($this->_request->getParams())) {
				$result = $this->_service->acl_providerSubscribeUser($form->getValue('id'), $form->getValue('email'));
				$this->_redirect('/course/invitesuccess/' . $this->_request->getParam('id'));
			}
		}

		$this->view->form = $form;
	}

	public function invitesuccessAction() {
		$courseObj = $this->_service->find($this->_request->getParam('id'));
		if (!is_object($courseObj)) {
			throw new \exception("Course not found with the ID provided.");
		}
		$this->view->course = $courseObj;
	}

	public function settingsAction() {
		if (null !== $this->_request->getParam('id')) {
			$course = $this->_service->find($this->_request->getParam('id'));
			if (!is_object($course)) {
				$course = $this->_service->findOneByTitle($this->_request->getParam('id'));
			}
		}
		if (!is_object($course)) {
			$this->view->page = "Course not found";
			$this->_response->setHttpResponseCode(404);
			$this->view->message = "Unable to find that course.";
		} else {
			$this->view->page = $course->getTitle();
			$this->view->course = $course;
			$this->view->content = $course->getContent();
			$this->view->current = "settings";
			if (\Zend_Auth::getInstance()->hasIdentity()) {
				$subscriptions = $this->_service->acl_findSubscriptions();
				if (is_array($subscriptions)) {
					foreach ($subscriptions as $key => $subscriptionObj) {
						if ($subscriptionObj->getCourse()->getId() == $course->getId()) {
							$this->view->page = $course->getTitle();
							break;
						}
					}
				}
			}
		}
	}

	public function pictureAction() {
		if (null !== $this->_request->getParam('id')) {
			$course = $this->_service->find($this->_request->getParam('id'));
			if (!is_object($course)) {
				$course = $this->_service->findOneByTitle($this->_request->getParam('id'));
			}
		}
		if (!is_object($course)) {
			$this->view->page = "Course not found";
			$this->_response->setHttpResponseCode(404);
			$this->view->message = "Unable to find that course.";
		} else {
			$this->view->page = $course->getTitle();
			$this->view->course = $course;
			$this->view->content = $course->getContent();
			$this->view->current = "picture";
			$form = new \App\Form\Course\UpdateImage();

			if ($this->_request->isPost()) {
				if ($form->isValid($this->_request->getParams())) {
					$this->_service->acl_updateImage($this->_request->getParam('id'));
				}
			}

			$this->view->form = $form;
		}
	}

	public function curriculumAction() {
		if (null !== $this->_request->getParam('id')) {
			$course = $this->_service->find($this->_request->getParam('id'));
			if (!is_object($course)) {
				$course = $this->_service->findOneByTitle($this->_request->getParam('id'));
			}
		}
		if (!is_object($course)) {
			$this->view->page = "Course not found";
			$this->_response->setHttpResponseCode(404);
			$this->view->message = "Unable to find that course.";
		} else {
			$this->view->page = $course->getTitle();
			$this->view->course = $course;
			$this->view->content = $course->getContent();
			$this->view->completedContent = array();
			$this->view->current = "curriculum";

			$sortedContent = array();

			// Chapters
			$chapters = $course->getChapters();
			$this->view->sortedContent = $this->_service->acl_listContentOrder($course->getId());

			if (\Zend_Auth::getInstance()->hasIdentity()) {
				$subscriptions = $this->_service->acl_findSubscriptions();
				if (is_array($subscriptions)) {
					foreach ($subscriptions as $key => $subscriptionObj) {
						if ($subscriptionObj->getCourse()->getId() == $course->getId()) {
							$this->view->page = $course->getTitle();
							$this->view->completedContent = $subscriptionObj->getCompletedContent();
							break;
						}
					}
				}
			}
		}
	}

	public function addchapterAction() {
		if (null !== $this->_request->getParam('id')) {
			$course = $this->_service->find($this->_request->getParam('id'));

			$this->view->page = "Add a new Chapter";
			if (!is_object($course)) {
				$course = $this->_service->findOneByTitle($this->_request->getParam('id'));
			}
		}
		if (!is_object($course)) {
			$this->view->page = "Course not found";
			$this->_response->setHttpResponseCode(404);
			$this->view->message = "Unable to find that course.";
		}

		$form = new \App\Form\Course\AddChapter();
		$form->populate(array('chapterId' => $course->getId()));
		if ($this->_request->isPost()) {
			if ($form->isValid($this->_request->getParams())) {
				if ($this->_service->acl_addChapter($course->getId(), $form->getValue("text"), $form->getValue("priority"))) {
					$this->_redirect("/course/curriculum/" . $course->getId());
				}
			}
		}
		$this->view->course = $course;

		$this->view->form = $form;
	}
}
