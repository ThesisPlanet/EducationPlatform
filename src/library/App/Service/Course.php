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
namespace App\Service;

class Course extends Base implements iCourse {

	const USER_MUST_IMPLEMENT_GETSUBSCRIPTIONS = "The user object must provide the getSubscriptions method.";

	const PERMISSION_DENIED = "You do not have permission to perform that action.";

	const ANNOUNCEMENT_NOT_FOUND = "Unable to locate that announcement";

	const COURSE_NOT_FOUND = "That course was not found.";

	const SUBSCRIPTION_NOT_FOUND = "That subscription was not found.";

	const REVIEW_NOT_FOUND = "That review was not found.";

	const CONTENT_DOES_NOT_BELONG = "The content ID provided does not belong to the course ID provided.";

	const CHAPTER_NOT_FOUND = "Unable to locate that chapter";

	const MUST_SUPPLY_ARRAY = "You must provide an array of chapters in order to update their priorities";

	const PERMISSION_DENIED_BY_INITIALIATION_USERS_SETTINGS = "The administrator has limited course creation to administrators only.";

	protected $_acl;

	protected $_em = null;

	protected $_announcementRepository = null;

	protected $_repository = null;

	protected $_reviewRepository = null;

	protected $_subscriptionRepository = null;

	protected $_subscriptionApprovalRepository = null;

	protected $_userRepository = null;

	protected $_form = null;

	protected $_deleteForm;

	protected $_user = null;

	protected $_addChapterForm = null;

	public function __construct() {
		$this->_em = \Zend_Registry::get('em');
		$this->_acl = new \App\Service\ACL\Course();

		// Lets try to modify the acl based on the configuration setting for
		// course creation.

		$config = \Zend_Registry::getInstance()->get('users');
		if ($config->canCreateCourses == false) {
			$this->_acl->removeAllow('user', null, 'acl_create');
			$this->_acl->allow('admin', null, 'acl_create');
		}

		$this->_repository = $this->_em->getRepository('\App\Entity\Course');
		$this->_assessmentRepository = $this->_em->getRepository('\App\Entity\Course\Assessment');
		$this->_announcementRepository = $this->_em->getRepository('\App\Entity\Course\Announcement');
		$this->_reviewRepository = $this->_em->getRepository('\App\Entity\Course\Review');
		$this->_subscriptionRepository = $this->_em->getRepository('\App\Entity\Subscription');
		$this->_subscriptionApprovalRepository = $this->_em->getRepository('\App\Entity\Subscription\Approval');
		$this->_chapterRepository = $this->_em->getRepository('\App\Entity\Course\Chapter');
		$this->_userRepository = $this->_em->getRepository('\App\Entity\User');

		$this->_contentRepository = $this->_em->getRepository('\App\Entity\Content');
	}

	public function isAllowed($courseObj, $action) {
		if (!is_object($this->_user)) {
			throw new \exception(self::USER_NOT_OBJECT);
		}
		if (!method_exists($this->_user, 'getSubscriptions')) {
			throw new \exception(self::USER_MUST_IMPLEMENT_GETSUBSCRIPTIONS);
		}
		$userService = new \App\Service\User();
		if (!is_object($courseObj)) {
			$role = $userService->authorize($this->_user->getId());
			if ($this->_acl->isAllowed($role, null, $action)) {
				return true;
			} else {
				return false;
			}
		} else {
			if (!method_exists($courseObj, 'getId')) {
				throw new \exception(self::INVALID_PROTECTED_OBJECT);
			}
			$subs = $this->_user->getsubscriptions();
			foreach ($subs as $key => $subscriptionObject) {
				if ($subscriptionObject->getCourse()->getId() === $courseObj->getId()) {
					if ($this->_acl->isAllowed($subscriptionObject->getRole(), null, $action)) {
						return true;
					}
				}
			}
		}
		// User-level role overrides enable employees to be able to perform
		// actions system-wide.
		$role = $userService->authorize($this->_user->getId());
		if ($this->_acl->isAllowed($role, null, $action)) {
			return true;
		}
		return false;
	}

	public function find($id) {
		return $this->_repository->find($id);
	}

	public function findAll() {
		return $this->_repository->findAllEnabledOrderedByTitle();
	}

	public function findMostPopular() {
		return $this->_repository->findAllEnabledOrderedBySubscriberCount();
	}

	public function findAllEnabledTopics() {
		return $this->_repository->findAllTopicsFromEnabledCourses();
	}

	public function findMostRecent() {
		return $this->_repository->findAllEnabledOrderedByRecency();
	}

	public function findByTopic($topic) {
		return $this->_repository->findByTopic($topic);
	}

	public function search($terms) {
		return $this->_repository->search($terms);
	}

	public function getForm($courseId = null) {
		if (null === $this->_form) {
			$this->_form = new \App\Form\Course();
		}
		if (null !== $courseId) {
			$obj = $this->find($courseId);
			$this->_form->populate($obj->toArray());
		}
		return $this->_form;
	}

	public function getAddChapterForm() {
		if (null === $this->_addChapterForm) {
			$this->_addChapterForm = new \App\Form\Course\AddChapter();
		}
		return $this->_addChapterForm;
	}

	/**
	 *
	 * @param integer $courseId            
	 * @param string $text            
	 * @return boolean
	 */
	public function acl_addChapter($courseId, $text) {
		$obj = $this->_repository->find($courseId);
		if (is_object($obj)) {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
		$form = new \App\Form\Course\AddChapter();

		if (!$form->isValid(array('courseId' => $obj->getId(), 'text' => $text))) {
			throw new \exception(self::FORM_INVALID);
		} else {
			$chapter = new \App\Entity\Course\Chapter();
			$chapter->setCourse($obj);
			$chapter->setText($form->getValue('text'));
			$chapter->setPriority(null);
			$this->_em->persist($chapter);
			$this->_em->flush();
			return $chapter->getId();
		}
	}

	public function acl_deleteChapter($chapterId) {
		$chapterObj = $this->_chapterRepository->find($chapterId);
		if (!is_object($chapterObj)) {
			throw new \exception(self::CHAPTER_NOT_FOUND);
		} else {
			$obj = $chapterObj->getCourse();
		}
		if (!is_object($obj)) {
			throw new \exception(self::COURSE_NOT_FOUND);
		} else {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		}
		// Unset the chapter ID form the content.
		foreach ($chapterObj->getContent() as $key => $contentObj) {
			$contentObj->setChapter(null);
			$this->_em->persist($contentObj);
		}
		$this->_em->flush();
		$this->_em->remove($chapterObj);
		$this->_em->flush();
		return true;
	}

	public function acl_listChapters($courseId) {
		$obj = $this->_repository->find($courseId);
		if (is_object($obj)) {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
		$chapterList = $this->_chapterRepository->findBy(array('_course' => $obj), array('priority' => 'asc'));

		$unassignedContent = $this->_contentRepository
				->findBy(array('_course' => $obj, '_chapter' => null, 'status' => 'ready'), array('sortOrder' => 'asc'));

		$mockChapter = new \App\Entity\Course\Chapter();
		$mockChapter->setText("unassigned");
		$mockChapter->setContent($unassignedContent);
		$mockChapter->setPriority(0);
		$chapterList[] = $mockChapter;

		return $chapterList;
	}

	/**
	 * Update the priority of each chapter.
	 *
	 * @param unknown_type $courseId            
	 * @param unknown_type $chapterIDsArray            
	 */
	public function acl_updateChapterOrder($courseId, $chapterIDsArray) {
		$obj = $this->_repository->find($courseId);
		if (is_object($obj)) {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		if (!is_array($chapterIDsArray)) {
			throw new \exception(self::MUST_SUPPLY_ARRAY);
		}
		$count = 0;
		foreach ($chapterIDsArray as $chapterID) {
			// Priority starts at 1.

			$chapterObj = $this->_chapterRepository->find($chapterID);
			if (!is_object($chapterObj)) {
				// Ignore if its not an object.
			} else {
				if ($chapterObj->getCourse()->getId() != $obj->getId()) {
					// Ignore because it doesn't belong to the course being
					// modified.
				}
				$count++;
				$chapterObj->setPriority($count);
				$this->_em->persist($chapterObj);
			}
		}
		$this->_em->flush();
		return true;
	}

	public function acl_providerUpdateContentSort($courseId, $contentIDsInChapterIDsArray) {
		/**
		 * sortArr format:
		 *
		 * array (ChapterOrder => array (chapterTitle => chapterTitle, content
		 * => (n => contentID)))
		 */

		// 1. Check permissions
		$obj = $this->_repository->find($courseId);
		if (is_object($obj)) {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		if (!is_array($contentIDsInChapterIDsArray)) {
			throw new \exception(self::MUST_SUPPLY_ARRAY);
		}
		$chapterOrder = 0;
		foreach ($contentIDsInChapterIDsArray as $chapterId => $arrOfContentIDs) {
			$chapterOrder++;
			$chapterObj = $this->_chapterRepository->find($chapterId);
			if (!is_object($chapterObj)) {
				if (!is_array($arrOfContentIDs)) {
				} else {
					$sortcount = 0;
					foreach ($arrOfContentIDs as $contentID => $contentType) {

						switch ($contentType) {
						case 'App\\Entity\\Course\\Assessment':
							$contentObj = $this->_assessmentRepository->find($contentID);
							break;
						default:
							$contentObj = $this->_contentRepository->find($contentID);
						}

						if (!is_object($contentObj)) {
							// no object was found. ignore
						} else {
							if ($contentObj->getCourse()->getId() != $obj->getId()) {
								// content Object doesn't belong to coures.
							} else {
								$sortcount++;
								$contentObj->setSortOrder($sortcount);
								$contentObj->setChapter(null);
								$this->_em->persist($contentObj);
								$this->_em->flush();
							}
						}
					}
				}
			} else {
				if ($chapterObj->getCourse()->getId() != $obj->getId()) {
					// an exception could be thrown here... but let's just
					// ignore it.
				}

				// the Chapter exists and is owned by this course. Time to start
				// processing the items in the chapter list

				$chapterObj->setPriority($chapterOrder);
				$this->_em->persist($chapterObj);
				$this->_em->flush();
				if (!is_array($arrOfContentIDs)) {
				} else {
					$sortcount = 0;
					foreach ($arrOfContentIDs as $contentID => $contentType) {

						switch ($contentType) {
						case 'App\\Entity\\Course\\Assessment':
							$contentObj = $this->_assessmentRepository->find($contentID);
							break;
						default:
							$contentObj = $this->_contentRepository->find($contentID);
						}
						if (!is_object($contentObj)) {
							// no object was found. ignore
						} else {
							if ($contentObj->getCourse()->getId() != $obj->getId()) {
								// content Object doesn't belong to coures.
							} else {
								$sortcount++;
								$contentObj->setSortOrder($sortcount);
								$contentObj->setChapter($chapterObj);
								$this->_em->persist($contentObj);
								$this->_em->flush();
							}
						}
					}
				}
			}
		}

		return $this->acl_listContentOrder($obj->getId());
	}

	public function getDeleteForm() {
		if (null === $this->_deleteForm) {
			$this->_deleteForm = new \App\Form\CourseDelete();
		}
		return $this->_deleteForm;
	}

	public function findOneByTitle($title) {
		return $this->_repository->findOneByTitle($title);
	}

	public function acl_create(array $data) {
		if (!$this->isAllowed(null, __FUNCTION__)) {
			throw new \exception(self::PERMISSION_DENIED);
		}

		$form = $this->getForm();
		$form->getElement('title')->addValidator(new \App\Validate\Course\Title());
		// $form->setSubmitLabel("Update");
		if ($form->isValid($data)) {
			$obj = new \App\Entity\Course();
			$obj->setTitle($data['title']);
			$obj->setDescription($data['description']);
			$obj->setTopic($data['topic']);
			$obj->setIsSearchable($data['isSearchable']);
			$obj->setIsApprovalRequired($data['isApprovalRequired']);
			$obj->setIsPublished(false);
			$obj->setIsEnabled(true);
			try {
				$this->_em->persist($obj);
				$this->_em->flush();
				$this->_message('create_success');

				// CREATE A SUBSCRIPTION + SET ROLE

				if (is_object($obj)) {
					if (!$this->isAllowed($obj, __FUNCTION__)) {
						throw new \exception(self::PERMISSION_DENIED);
					}
				} else {
					throw new \exception(self::COURSE_NOT_FOUND);
				}
				$subscriptionObj = new \App\Entity\Subscription();
				$subscriptionObj->setCourse($obj);
				$subscriptionObj->setUser($this->_user);
				$subscriptionObj->setIsEnabled(true);
				$subscriptionObj->setRole('provider');

				$this->_em->persist($subscriptionObj);
				$this->_em->flush();
				$this->_user->getSubscriptions()->add($subscriptionObj);
				$obj->getSubscriptions()->add($subscriptionObj);
				return $obj->getId();
			} catch (\exception $e) {
				echo "Unable to create. " . $e->getMessage();
				return false;
			}
		} else {
			throw new \exception("Form invalid" . print_r($form->getMessages(), true));
		}
	}
	/*
	 * (non-PHPdoc) @see \App\Service\iCourse::acl_deleteCourse()
	 */
	public function acl_delete($id) {
		$obj = $this->_repository->find($id);
		if (is_object($obj)) {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		$subscriptionList = $obj->getSubscriptions();
		foreach ($subscriptionList as $key => $subscriptionObj) {
			$this->_em->remove($subscriptionObj);
			$this->_em->flush();
		}

		foreach ($obj->getContent() as $key => $contentObj) {
			$this->_em->remove($contentObj);
			$this->_em->flush();
		}

		foreach ($obj->getAssessments() as $key => $assessmentObj) {
			foreach ($assessmentObj->getQuestions() as $keyQuestion => $questionObj) {
				$this->_em->remove($questionObj);
				$this->_em->flush();
			}
			$this->_em->remove($assessmentObj);
			$this->_em->flush();
		}

		$this->_em->remove($obj);
		$this->_em->flush();
		return true;
	}

	public function acl_update($id, array $data) {
		$obj = $this->_repository->find($id);
		if (is_object($obj)) {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
		$form = $this->getForm();
		$form->setSubmitLabel("Update");
		if ($form->isValid($data)) {
			$obj->setTitle($data['title']);
			$obj->setTopic($form->getValue('topic'));
			$obj->setDescription($data['description']);
			$obj->setIsApprovalRequired($data['isApprovalRequired']);
			$obj->setIsSearchable($data['isSearchable']);
			$this->_em->persist($obj);
			$this->_em->flush();
			$this->_message('update_success');
			return true;
		} else {
			throw new \exception('Invalid input was received.');
		}
	}
	/*
	 * (non-PHPdoc) @see \App\Service\iCourse::acl_publishCourse()
	 */
	public function acl_publish($courseId) {
		$courseObj = $this->_repository->find((int) $courseId);

		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
		$courseObj->setIsPublished(true);
		$this->_em->persist($courseObj);
		$this->_em->flush();
		return true;
	}

	/*
	 * (non-PHPdoc) @see \App\Service\iCourse::acl_unpublishCourse()
	 */
	public function acl_unpublish($courseId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
		$courseObj->setIsPublished(false);
		$this->_em->persist($courseObj);
		$this->_em->flush();
		return true;
	}

	/*
	 * (non-PHPdoc) @see \App\Service\iCourse::acl_disableCourse()
	 */
	public function acl_disable($courseId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
		$courseObj->setIsEnabled(false);
		$this->_em->persist($courseObj);
		$this->_em->flush();
		return true;
	}

	/*
	 * (non-PHPdoc) @see \App\Service\iCourse::acl_enableCourse()
	 */
	public function acl_enable($courseId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		$courseObj->setIsEnabled(true);
		$this->_em->persist($courseObj);
		$this->_em->flush();
		return true;
	}

	/*
	 * (non-PHPdoc) @see \App\Service\iCourse::acl_addStudentReview()
	 */
	public function acl_addReview($courseId, $isRecommended, $reviewText) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		// Check to make sure that there isn't a review existing already. If one
		// does then replace it.
		$list = $this->_reviewRepository->findBy(array('_course' => $courseObj, '_user' => $this->_user));
		if (count($list) >= 1) {
			foreach ($list as $key => $reviewObjToRemove) {
				$this->_em->remove($reviewObjToRemove);
			}
		}
		$review = new \App\Entity\Course\Review();
		$review->setComment($reviewText);
		$review->setCourse($courseObj);
		$review->setUserRecommends($isRecommended);
		$this->_em->persist($review);
		$this->_em->flush();
		return $review->getId();
	}
	/*
	 * (non-PHPdoc) @see \App\Service\iCourse::acl_removeStudentReview()
	 */
	public function acl_removeReview($reviewId) {
		$reviewObj = $this->_reviewRepository->find($reviewId);
		if (is_object($reviewObj)) {
			$courseObj = $reviewObj->getCourse();
		} else {
			throw new \exception(self::REVIEW_NOT_FOUND);
		}

		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
		$this->_em->remove($reviewObj);
		$this->_em->flush();
		return true;
	}

	public function acl_subscribe($courseId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
		if ($courseObj->getIsApprovalRequired() == true) {
			// TODO: Figure out what to do with a course requiring approval.
		} // COURSE PROVIDER APPROVAL IS NOT REQUIRED
 else {
			$subscriptionList = $courseObj->getSubscriptions();
			foreach ($subscriptionList as $key => $subscriptionObj) {
				if ($subscriptionObj->getUser()->getId() === $this->_user->getId()) {
					return true;
				}
			}
			$subscriptionObj = new \App\Entity\Subscription();
			$subscriptionObj->setCourse($courseObj);
			$subscriptionObj->setUser($this->_user);
			$subscriptionObj->setIsEnabled(true);
			$subscriptionObj->setRole('subscriber');
			$subscriptionObj->setCompletedContent(array());

			$this->_user->getSubscriptions()->add($subscriptionObj);
			$courseObj->getSubscriptions()->add($subscriptionObj);
			$this->_em->persist($courseObj);
			$this->_em->persist($subscriptionObj);
			$this->_em->persist($this->_user);
			$this->_em->flush();

			return true;
		}
		return false;
	}

	public function acl_unsubscribe($courseId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		$subscriptionObj = $this->_subscriptionRepository->findOneBy(array('_user' => $this->_user, '_course' => $courseObj));
		if (!is_object($subscriptionObj)) {
			throw new \exception(self::SUBSCRIPTION_NOT_FOUND);
		} else {
			$this->_em->remove($subscriptionObj);
			$this->_em->flush();
			return true;
		}
		return false;
	}

	public function acl_approveSubscribeRequest($courseId, $userId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
	}

	public function acl_denySubscribeRequest($courseId, $userId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
	}

	public function acl_userAcceptInvite($courseId, $userId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
	}

	public function acl_userRejectInvite($courseId, $userId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
	}

	public function acl_findSubscriptions() {
		if (!$this->isAllowed(null, __FUNCTION__)) {
			throw new \exception(self::PERMISSION_DENIED);
		}
		return $this->_subscriptionRepository->findByUser($this->_user);
	}

	public function acl_findSubscription($courseId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
		return $this->_subscriptionRepository->findOneBy(array('_user' => $this->_user, '_course' => $courseObj));
	}

	public function acl_updateImage($courseId) {
		$obj = $this->_repository->find($courseId);
		if (is_object($obj)) {
			if (!$this->isAllowed($obj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::NOT_FOUND);
		}
		$form = new \App\Form\Course\UpdateImage();

		$data = array('id' => $obj->getId(), 'title' => $obj->getTitle());

		if ($form->isValid($data)) {

			$oldname = \pathinfo($form->file->getFileName());

			$newname = SHARE_PATH . DIRECTORY_SEPARATOR . 'course' . DIRECTORY_SEPARATOR . "image" . DIRECTORY_SEPARATOR . $obj->getId() . '_tmp.' . $oldname['extension'];

			$finalname = SHARE_PATH . DIRECTORY_SEPARATOR . 'course' . DIRECTORY_SEPARATOR . "image" . DIRECTORY_SEPARATOR . $obj->getId() . '.png';

			$form->file->addFilter('Rename', array('target' => $newname, 'overwrite' => true));

			$form->getValues();

			$form->file->getTransferAdapter()->setOptions(array('useByteString' => false));

			$form->convertImage($newname, $finalname);
			$form->uploadImage($finalname);

			$this->_message('update_success');
			return true;
		} else {
			throw new \exception(self::FORM_INVALID);
		}
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \App\Service\iCourse::acl_completeContent()
	 */
	public function acl_completeContent($courseId, $contentId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
		$subscriptionList = $this->_user->getSubscriptions();

		$validSubscription = null;
		foreach ($subscriptionList as $key => $subscriptionObj) {
			if ($subscriptionObj->getCourse()->getId() == $courseId) {
				$validSubscription = $subscriptionObj;
				break;
			}
		}
		if ($validSubscription == null) {
			throw new \exception(self::SUBSCRIPTION_NOT_FOUND);
		}
		// Check to see if the given piece of content belongs to the course.

		$content = $courseObj->getContent();
		$validContentObj = null;
		foreach ($content as $key => $contentObj) {
			if ($contentId == $contentObj->getId()) {
				$validContentObj = $contentObj;
				break;
			}
		}
		if ($validContentObj == null) {
			throw new \exception(self::CONTENT_DOES_NOT_BELONG);
		}
		// OK, we now have a valid subscription object.
		$completedContent = $validSubscription->getCompletedContent();
		if (!is_array($completedContent)) {
			$completedContent = array();
		}

		$completedContent[$validContentObj->getId()] = $validContentObj->getId();
		$validSubscription->setCompletedContent($completedContent);
		$this->_em->persist($validSubscription);
		$this->_em->flush();
		return true;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \App\Service\iCourse::acl_uncompleteContent()
	 */
	public function acl_uncompleteContent($courseId, $contentId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
		$subscriptionList = $this->_user->getSubscriptions();

		$validSubscription = null;
		foreach ($subscriptionList as $key => $subscriptionObj) {
			if ($subscriptionObj->getCourse()->getId() == $courseId) {
				$validSubscription = $subscriptionObj;
				break;
			}
		}
		if ($validSubscription == null) {
			throw new \exception(self::SUBSCRIPTION_NOT_FOUND);
		}
		// Check to see if the given piece of content belongs to the course.

		$content = $courseObj->getContent();
		$validContentObj = null;
		foreach ($content as $key => $contentObj) {
			if ($contentId == $contentObj->getId()) {
				$validContentObj = $contentObj;
				break;
			}
		}
		if ($validContentObj == null) {
			throw new \exception(self::CONTENT_DOES_NOT_BELONG);
		}
		// OK, we now have a valid subscription object.
		$completedContent = $validSubscription->getCompletedContent();
		if (!is_array($completedContent)) {
			$completedContent = array();
		}

		unset($completedContent[$validContentObj->getId()]);
		$validSubscription->setCompletedContent($completedContent);
		$this->_em->persist($validSubscription);
		$this->_em->flush();
		return true;
	}

	public function acl_getCompletedContentList($courseId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}
		$subscriptionList = $this->_user->getSubscriptions();

		$validSubscription = null;
		foreach ($subscriptionList as $key => $subscriptionObj) {
			if ($subscriptionObj->getCourse()->getId() == $courseId) {
				$validSubscription = $subscriptionObj;
				break;
			}
		}
		if ($validSubscription == null) {
			throw new \exception(self::SUBSCRIPTION_NOT_FOUND);
		}

		// OK, we now have a valid subscription object.
		$completedContent = $validSubscription->getCompletedContent();
		if (!is_array($completedContent)) {
			$completedContent = array();
		}
		return $completedContent;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \App\Service\iCourse::acl_getPercentComplete()
	 */
	public function acl_getPercentComplete($courseId) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		$subscriptionList = $this->_user->getSubscriptions();

		$validSubscription = null;
		foreach ($subscriptionList as $key => $subscriptionObj) {
			if ($subscriptionObj->getCourse()->getId() == $courseId) {
				$validSubscription = $subscriptionObj;
				break;
			}
		}
		if ($validSubscription == null) {
			throw new \exception(self::SUBSCRIPTION_NOT_FOUND);
		}

		$content = $courseObj->getContent();
		foreach ($content as $key => $contentObj) {
			if ($contentObj->getIsPublished() == true) {
				$totalList[$contentObj->getId()] = $contentObj;
			}
		}
		$total = count($totalList);

		$assessments = $courseObj->getAssessments();
		$assessmentTotalCount = 0;
		$assessmentCompletedCount = 0;
		foreach ($assessments as $key => $assessmentObj) {
			$assessmentTotalCount++;
			$assessmentCompleted = false;
			$assessmentService = new \App\Service\Course\Assessment();
			$assessmentService->setUser($this->_user);
			$attempts = $assessmentService->acl_listAttempts($assessmentObj->getId());

			foreach ($attempts as $attemptKey => $attemptObj) {
				if ($attemptObj->getFinishedAt() instanceof \DateTime) {
					$assessmentCompleted = true;
				}
			}
			if ($assessmentCompleted == true) {
				$assessmentCompletedCount++;
			}
		}
		$total = $total + $assessmentTotalCount;

		$completedArr = $validSubscription->getCompletedContent();
		$completedCount = count($validSubscription->getCompletedContent()) + $assessmentCompletedCount;
		if ($total != 0) {
			$percentComplete = $completedCount / $total;
		} else {
			$percentComplete = 0;
		}
		if ($percentComplete > 100) {
			$percentComplete = 100;
		}
		if ($percentComplete < 0) {
			$percentComplete = 0;
		}
		return round($percentComplete, 2);
	}
	/*
	 * (non-PHPdoc) @see \App\Service\iCourse::acl_providerSubscribeUser()
	 */
	public function acl_providerSubscribeUser($courseId, $email) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		$userObj = $this->_userRepository->findOneByEmail($email);
		if (!is_object($userObj)) {
			throw new \exception(self::USER_NOT_FOUND);
		}
		/**
		 * $subscriptionApproval = new \App\Entity\Subscription\Approval();
		 * $subscriptionApproval->setCourse($courseObj);
		 * $subscriptionApproval->setCourseApproved(true);
		 * $subscriptionApproval->setRole('subscriber');
		 * $subscriptionApproval->setUserApproved(false);
		 */
		$url = \Zend_Registry::getInstance()->get('system')->PUBLIC_SERVER_NAME . "/course/view/" . $courseObj->getId();

		// send an e-mail
		$mail = new \TP\Communication\Email();
		$mail->assignTemplateParam("email", $userObj->getEmail())->assignTemplateParam("firstname", $userObj->getFirstname())->assignTemplateParam("course", $courseObj)->assignTemplateParam("url", $url)->assignTemplateParam("user", $userObj)->addTo($userObj->getEmail())
				->setSubject("You are invited to take a course titled, " . $courseObj->getTitle());
		$mail->sendHtmlTemplate("inviteUserToCourse.phtml");
		return true;
	}

	/*
	 * (non-PHPdoc) @see \App\Service\iCourse::acl_providerDeleteSubscription()
	 */
	public function acl_providerDeleteSubscription($subscriptionId) {
		$form = new \App\Form\SubscriptionDelete();
		$subscriptionObj = $this->_subscriptionRepository->find($subscriptionId);
		if (!is_object($subscriptionObj)) {
			throw new \exception(self::SUBSCRIPTION_NOT_FOUND);
		} else {
			$data = array('id' => $subscriptionId);
			if (!$form->isValid($data)) {
				throw new \exception(self::FORM_INVALID);
			}
			if ($this->isAllowed($subscriptionObj->getCourse(), __FUNCTION__)) {
				$this->_em->remove($subscriptionObj);
				$this->_em->flush();
				return true;
			} else {
				throw new \exception(self::PERMISSION_DENIED);
			}
		}
	}

	/*
	 * (non-PHPdoc) @see
	 * \App\Service\iCourse::acl_providerUpdateSubscriptionRole()
	 */
	public function acl_providerUpdateSubscriptionRole($subscriptionId, $newRole) {
		$form = new \App\Form\Subscription();
		$subscriptionObj = $this->_subscriptionRepository->find($subscriptionId);
		if (!is_object($subscriptionObj)) {
			throw new \exception(self::SUBSCRIPTION_NOT_FOUND);
		} else {
			$data = $subscriptionObj->toArray();
			$data['role'] = $newRole;
			if (!$form->isValid($data)) {
				throw new \exception(self::FORM_INVALID);
			}
			if ($this->isAllowed($subscriptionObj->getCourse(), __FUNCTION__)) {

				$subscriptionObj->setRole($form->getValue('role'));
				$this->_em->persist($subscriptionObj);
				$this->_em->flush();
				return true;
			} else {
				throw new \exception(self::PERMISSION_DENIED);
			}
		}
	}

	public function acl_providerFindSubscription($subscriptionId) {
		$subscriptionObj = $this->_subscriptionRepository->find($subscriptionId);
		if (!is_object($subscriptionObj)) {
			throw new \exception(self::SUBSCRIPTION_NOT_FOUND);
		} else {
			if ($this->isAllowed($subscriptionObj->getCourse(), __FUNCTION__)) {
				return $subscriptionObj;
			} else {
				throw new \exception(self::PERMISSION_DENIED);
			}
		}
	}
	/*
	 * (non-PHPdoc) @see \App\Service\iCourse::acl_providerCreateAnnouncement()
	 */
	// TODO: Enable some kind of notification mechanism
	public function acl_providerCreateAnnouncement($courseId, $announcementText, $shouldNotifyUsers) {
		$courseObj = $this->_repository->find((int) $courseId);
		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		$form = new \App\Form\Course\Announcement();
		if (!$form->isValid(array('text' => $announcementText, 'notify' => $shouldNotifyUsers))) {
			throw new \exception(self::FORM_INVALID);
		}
		$announcementObj = new \App\Entity\Course\Announcement();
		$announcementObj->setCourse($courseObj);
		$announcementObj->setText($form->getValue('text'));
		$announcementObj->setCreatedAt(new \DateTime());
		$announcementObj->setUser($this->_user);
		$this->_em->persist($announcementObj);
		$this->_em->flush();

		// Send emails

		try {
			$cl = \Zend_Registry::getInstance()->get('queue');
			$jobParams = array('environment' => APPLICATION_ENV, 'templateName' => "CourseAnnouncement.phtml", 'emailSubject' => "A course announcement has been posted for: " . $courseObj->getTitle(), 'courseId' => $courseObj->getId(),
					'templateParameters' => array('course' => array('id' => $courseObj->getId(), 'title' => $courseObj->getTitle()),
							'announcer' => array('id' => $this->_user->getId(), 'firstname' => $this->_user->getFirstname(), 'lastname' => $this->_user->getLastname()),
							'announcement' => array('id' => $announcementObj->getId(), 'text' => $announcementObj->getText())));
			$cl->backgroundTask('DEP_Notification_SendCourseWideNotification', $jobParams);
		} catch (\exception $e) {
			throw new \exception("There was a problem creating the processing task to handle the
              post-upload activities." . $e->getMessage());
		}

		return $announcementObj->getId();
	}

	/*
	 * (non-PHPdoc) @see \App\Service\iCourse::acl_providerRemoveAnnouncement()
	 */
	public function acl_providerRemoveAnnouncement($announcementId) {
		$announcementObj = $this->_announcementRepository->find($announcementId);
		if (!is_object($announcementObj)) {
			throw new \exception(self::ANNOUNCEMENT_NOT_FOUND);
		}
		$courseObj = $announcementObj->getCourse();

		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		$this->_em->remove($announcementObj);
		$this->_em->flush();
		return true;
	}

	/*
	 * (non-PHPdoc) @see \App\Service\iCourse::acl_listAnnouncements()
	 */
	public function acl_listAnnouncements($courseId) {
		$courseObj = $this->_repository->find((int) $courseId);

		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		$announcements = $this->_announcementRepository->findBy(array('_course' => $courseObj), array('createdAt' => 'DESC'));
		return $announcements;
	}

	public function acl_findAnnouncement($announcementId) {
		$announcement = $this->_announcementRepository->find($announcementId);

		if (!is_object($announcement)) {
			throw new \exception(self::ANNOUNCEMENT_NOT_FOUND);
		}

		$courseObj = $announcement->getCourse();

		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		return $announcement;
	}

	public function acl_getImageUrl($courseId, $options = array()) {
		$opt = array();
		if (file_exists(SHARE_PATH . DIRECTORY_SEPARATOR . "course" . DIRECTORY_SEPARATOR . "image" . DIRECTORY_SEPARATOR . (int) $courseId . ".png")) {

			$opt['Secure'] = false;
			if (!isset($options['minutes'])) {
				$options['minutes'] = null;
				$numberOfMinutes = 30;
			} else {
				$numberOfMinutes = (int) $options['minutes'];
			}
			if (!isset($options['IPAddress'])) {
			} else {
				$opt['IPAddress'] = $options['IPAddress'];
			}
			$filename = "course/image/" . (int) $courseId . ".png";
			$cf = new \AmazonCloudFront();
			$distribution_hostname = \Zend_Registry::getInstance()->get('cloud')->aws->CLOUDFRONT_DOWNLOAD_DISTRIBUTION_URL;
			// Options should include restrictors such as IP Address, Duration,
			// etc.
			$expires = strtotime('+' . $numberOfMinutes . 'minutes'); // time
			$url = $cf->get_private_object_url($distribution_hostname, $filename, $expires, $opt);
			return $url;
		} else {
			$imgUrl = "/img/icons/course/partial_default.png";
		}

		return $imgUrl;
	}

	/**
	 *
	 * @param integer $courseId            
	 * @throws \exception
	 * @return array
	 */
	public function acl_listContentOrder($courseId) {
		$courseObj = $this->_repository->find((int) $courseId);

		if (is_object($courseObj)) {
			if (!$this->isAllowed($courseObj, __FUNCTION__)) {
				throw new \exception(self::PERMISSION_DENIED);
			}
		} else {
			throw new \exception(self::COURSE_NOT_FOUND);
		}

		$courseContent = $this->_contentRepository->findBy(array('_course' => $courseObj), array('sortOrder' => 'ASC'));

		$courseAssessments = $this->_assessmentRepository->findBy(array('_course' => $courseObj), array('sortOrder' => 'ASC'));

		$courseChapters = $courseObj->getChapters();

		$out = array();
		$outArrLength = 0;
		$out[$outArrLength] = array('chapter' => null, 'content' => array());
		foreach ($courseContent as $contentKey => $contentObj) {
			if (!is_object($contentObj->getChapter())) {
				$out[0]['content'][] = $contentObj->toArray();
			} else {
				if (!isset($out[$contentObj->getChapter()->getId()])) {
					$out[$contentObj->getChapter()->getId()] = array('chapter' => $contentObj->getChapter()->toArray(), 'content' => array());
				}
				$out[$contentObj->getChapter()->getId()]['content'][] = $contentObj->toArray();
			}
		}

		foreach ($courseAssessments as $assessmentKey => $assessmentObj) {
			if (!is_object($assessmentObj->getChapter())) {
				$out[0]['content'][] = $assessmentObj->toArray();
			} else {
				if (!isset($out[$assessmentObj->getChapter()->getId()])) {
					$out[$assessmentObj->getChapter()->getId()] = array('chapter' => $assessmentObj->getChapter()->toArray(), 'content' => array());
				}
				$out[$assessmentObj->getChapter()->getId()]['content'][] = $assessmentObj->toArray();
			}
		}

		foreach ($courseChapters as $chapterObj) {
			if (!array_key_exists($chapterObj->getId(), $out)) {
				$out[$chapterObj->getId()] = array('chapter' => $chapterObj->toArray(), 'content' => array());
			}
		}

		if (count($out[0]['content']) == 0) {
			unset($out[0]);
		}

		usort($out,
				function ($a, $b) {
					if (!is_array($a['chapter'])) {
						$a['chapter'] = array('priority' => 0);
					}
					if (!is_array($b['chapter'])) {
						$b['chapter'] = array('priority' => 0);
					}
					return ($a['chapter']['priority'] < $b['chapter']['priority']) ? -1 : 1;
				});

		$out = array_values($out);
		return $out;
	}
}
