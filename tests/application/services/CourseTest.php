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
namespace tests\application\services;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class CourseTest extends \Zend_Test_PHPUnit_ControllerTestCase {

	protected $_service = null;

	protected $_userService = null;

	public function setUp() {
		$this->_service = new \App\Service\Course();
		$this->_userService = new \App\Service\User();
		$users = \Zend_Registry::getInstance()->get('users');
		$users->canCreateCourses = true;

	}

	public function testFindMethods() {
		$this->assertInternalType('array', $this->_service->findAll());
		$this->assertInternalType('array', $this->_service->findMostPopular());
		$this->assertInternalType('array', $this->_service->findAllEnabledTopics());
		$this->assertInternalType('array', $this->_service->findMostRecent());
		$this->assertInternalType('array', $this->_service->findByTopic('Computer Science'));
		$this->assertInternalType('array', $this->_service->search('PHP'));
	}

	public function testSetUserNonObject() {
		$this->setExpectedException("exception", \App\Service\Course::USER_NOT_OBJECT);
		$this->_service->setUser(null);
	}

	public function testACLNoUser() {
		$this->setExpectedException("exception", \App\Service\Course::USER_NOT_OBJECT);
		$this->_service->isAllowed(null, "acl_getThumbnailUrl");
	}

	public function testACLNotUserObject() {
		$user = new \stdClass();

		$this->_service->setUser($user);

		$this->setExpectedException("exception", \App\Service\Course::USER_MUST_IMPLEMENT_GETSUBSCRIPTIONS);
		$this->_service->isAllowed(null, "acl_getThumbnailUrl");
	}

	public function testACLNotValidProtectedSubject() {
		$user = $this->_userService->findByEmail("testvisitor@thesisplanet.com");
		$this->_service->setUser($user);

		$subject = new \stdClass();

		$this->setExpectedException("exception", \App\Service\Course::INVALID_PROTECTED_OBJECT);
		$this->_service->isAllowed($subject, "acl_getThumbnailUrl");
	}

	public function testACL() {
		/**
		 * Visitor
		 */
		$user = $this->_userService->findByEmail("testvisitor@thesisplanet.com");
		$this->_service->setUser($user);

		// visitor
		$this->assertTrue($this->_service->isAllowed(null, "acl_getThumbnailUrl"));

		// user
		$this->assertFalse($this->_service->isAllowed(null, "acl_create"));

		// subscriber
		$this->assertFalse($this->_service->isAllowed(null, "acl_rate"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_comment"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_addReview"));

		// provider
		$this->assertFalse($this->_service->isAllowed(null, "acl_update"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_addCoupon"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_disableCoupon"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_unpublish"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_publish"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_addChapter"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_deleteChapter"));

		// admin
		$this->assertFalse($this->_service->isAllowed(null, "acl_delete"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_removeReview"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_enable"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_disable"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_removeCoupon"));

		/**
		 * User
		 */
		$user = $this->_userService->findByEmail("testuser@thesisplanet.com");
		$this->_service->setUser($user);

		// visitor
		$this->assertTrue($this->_service->isAllowed(null, "acl_getThumbnailUrl"));

		// user
		$this->assertTrue($this->_service->isAllowed(null, "acl_create"));

		// subscriber
		$this->assertFalse($this->_service->isAllowed(null, "acl_rate"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_comment"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_addReview"));

		// provider
		$this->assertFalse($this->_service->isAllowed(null, "acl_update"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_addCoupon"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_disableCoupon"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_unpublish"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_publish"));

		// admin
		$this->assertFalse($this->_service->isAllowed(null, "acl_delete"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_removeReview"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_enable"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_disable"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_removeCoupon"));

		/**
		 * Subscriber
		 */
		$user = $this->_userService->findByEmail("testsubscriber@thesisplanet.com");
		$this->_service->setUser($user);

		// visitor
		$this->assertTrue($this->_service->isAllowed(null, "acl_getThumbnailUrl"));

		// user
		$this->assertTrue($this->_service->isAllowed(null, "acl_create"));

		// subscriber
		$this->assertTrue($this->_service->isAllowed(null, "acl_rate"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_comment"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_addReview"));

		// provider
		$this->assertFalse($this->_service->isAllowed(null, "acl_update"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_addCoupon"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_disableCoupon"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_unpublish"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_publish"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_addChapter"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_deleteChapter"));

		// admin
		$this->assertFalse($this->_service->isAllowed(null, "acl_delete"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_removeReview"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_enable"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_disable"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_removeCoupon"));

		/**
		 * Provider
		 */

		$user = $this->_userService->findByEmail("testprovider@thesisplanet.com");
		$this->_service->setUser($user);

		// visitor
		$this->assertTrue($this->_service->isAllowed(null, "acl_getThumbnailUrl"));

		// user
		$this->assertTrue($this->_service->isAllowed(null, "acl_create"));

		// subscriber
		$this->assertTrue($this->_service->isAllowed(null, "acl_rate"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_comment"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_addReview"));

		// provider
		$this->assertTrue($this->_service->isAllowed(null, "acl_update"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_addCoupon"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_disableCoupon"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_unpublish"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_publish"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_addChapter"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_deleteChapter"));

		// admin
		$this->assertFalse($this->_service->isAllowed(null, "acl_delete"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_removeReview"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_enable"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_disable"));
		$this->assertFalse($this->_service->isAllowed(null, "acl_removeCoupon"));

		/**
		 * Admin
		 */

		$user = $this->_userService->findByEmail("testadmin@thesisplanet.com");
		$this->_service->setUser($user);

		// visitor
		$this->assertTrue($this->_service->isAllowed(null, "acl_getThumbnailUrl"));

		// user
		$this->assertTrue($this->_service->isAllowed(null, "acl_create"));

		// subscriber
		$this->assertTrue($this->_service->isAllowed(null, "acl_rate"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_comment"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_addReview"));

		// provider
		$this->assertTrue($this->_service->isAllowed(null, "acl_update"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_addCoupon"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_disableCoupon"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_unpublish"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_publish"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_addChapter"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_deleteChapter"));

		// admin
		$this->assertTrue($this->_service->isAllowed(null, "acl_delete"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_removeReview"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_enable"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_disable"));
		$this->assertTrue($this->_service->isAllowed(null, "acl_removeCoupon"));
	}

	public function testCourseCreateByAuthorizedUser() {
		$config = \Zend_Registry::getInstance()->get('users');
		if ($config->canCreateCourses == false) {
			$user = $this->_userService->findByEmail("testadmin@thesisplanet.com");
		} else {
			$user = $this->_userService->findByEmail("testuser@thesisplanet.com");
		}
		$this->_service->setUser($user);

		$courseData = array('topic' => "Computer Science", 'description' => "Course Testing", 'title' => "COURSE - How to create unit tested software", 'isApprovalRequired' => 0, 'isSearchable' => 0, 'isEnabled' => 1);

		$courseId = $this->_service->acl_create($courseData);
		// Expect an integer
		$this->assertInternalType('integer', $courseId);

		$user = $this->_userService->findByEmail('testadmin@thesisplanet.com');
		$this->_service->setUser($user);

		$result = $this->_service->acl_delete($courseId);

		$this->assertTrue($result);
	}

	public function testCourseChapters() {
		$courseData = array('topic' => "Computer Science", 'description' => "Course Testing", 'title' => "How to create unit tested software", 'isApprovalRequired' => 0, 'isSearchable' => 0, 'isEnabled' => 1, 'price' => 150);

		$user = $this->_userService->findByEmail("testuser@thesisplanet.com");
		$this->_service->setUser($user);

		$courseId = $this->_service->acl_create($courseData);
		// Expect an integer
		$this->assertInternalType('integer', $courseId);
		$chapterId = $this->_service->acl_addChapter($courseId, "sample chapter");

		$this->assertInternalType('integer', $chapterId);
		$this->assertTrue($this->_service->acl_deleteChapter($chapterId));

		$user = $this->_userService->findByEmail('testadmin@thesisplanet.com');
		$this->_service->setUser($user);

		$result = $this->_service->acl_delete($courseId);

		$this->assertTrue($result);
	}

	public function testCourseEnableDisableByAuthorizedUser() {
		$courseData = array('topic' => "Computer Science", 'description' => "Course Testing", 'title' => "How to create unit tested software", 'isApprovalRequired' => 0, 'isSearchable' => 0, 'isEnabled' => 1, 'price' => 150);

		$user = $this->_userService->findByEmail("testadmin@thesisplanet.com");
		$this->_service->setUser($user);

		$courseId = $this->_service->acl_create($courseData);
		// Expect an integer
		$this->assertInternalType('integer', $courseId);

		$this->assertTrue($this->_service->acl_disable($courseId));
		$this->assertTrue($this->_service->acl_enable($courseId));

		$user = $this->_userService->findByEmail('testadmin@thesisplanet.com');
		$this->_service->setUser($user);

		$result = $this->_service->acl_delete($courseId);

		$this->assertTrue($result);
	}

	public function testCourseAddRemoveStudentReview() {
		$courseData = array('topic' => "Computer Science", 'description' => "Course Testing", 'title' => "How to create unit tested software", 'isApprovalRequired' => 0, 'isSearchable' => 0, 'isEnabled' => 1, 'price' => 150);

		$user = $this->_userService->findByEmail("testsubscriber@thesisplanet.com");
		$this->_service->setUser($user);

		$courseId = $this->_service->acl_create($courseData);
		// Expect an integer
		$this->assertInternalType('integer', $courseId);

		$reviewId = $this->_service->acl_addReview($courseId, true, "This course was totally worth the money.");

		$this->assertInternalType('integer', $reviewId);

		$adminUser = $this->_userService->findByEmail("testadmin@thesisplanet.com");
		$this->_service->setUser($adminUser);

		$this->assertTrue($this->_service->acl_removeReview($reviewId));

		$this->assertTrue($this->_service->acl_delete($courseId));
	}

	public function testCourseCreateByUnAuthorizedUser() {
		$courseData = array('topic' => "Computer Science", 'description' => "Course Testing", 'title' => "How to create unit tested software", 'isApprovalRequired' => 0, 'isSearchable' => 0, 'isEnabled' => 1, 'price' => 150);

		$user = $this->_userService->findByEmail("testvisitor@thesisplanet.com");
		$this->_service->setUser($user);

		$this->setExpectedException("exception", \App\Service\Course::PERMISSION_DENIED);

		$this->_service->acl_create($courseData);
	}

	public function testCoursePublication() {
		$courseData = array('topic' => "Computer Science", 'description' => "Course Testing", 'title' => "How to create unit tested software", 'isApprovalRequired' => 0, 'isSearchable' => 0, 'isEnabled' => 1, 'price' => 150);

		$user = $this->_userService->findByEmail("testprovider@thesisplanet.com");
		$this->_service->setUser($user);

		$courseId = $this->_service->acl_create($courseData);
		// Expect an integer
		$this->assertInternalType('integer', $courseId);

		$this->assertTrue($this->_service->acl_publish($courseId));

		$this->assertTrue($this->_service->acl_unpublish($courseId));

		$adminUser = $this->_userService->findByEmail("testadmin@thesisplanet.com");
		$this->_service->setUser($adminUser);

		$this->assertTrue($this->_service->acl_delete($courseId));
	}

	public function testACLDeny() {
		$courseData = array('topic' => "Computer Science", 'description' => "Course Testing", 'title' => "How to create unit tested software", 'isApprovalRequired' => 0, 'isSearchable' => 0, 'isEnabled' => 1, 'price' => 150);

		$user = $this->_userService->findByEmail("testprovider@thesisplanet.com");
		$this->_service->setUser($user);

		$courseId = $this->_service->acl_create($courseData);

		try {
			$this->_service->acl_delete($courseId);
		} catch (\exception $e) {
			if ($e->getMessage() != \App\Service\Course::PERMISSION_DENIED) {
				$this->fail("Exception not passed." . $e->getMessage());
			}
		}
		$adminUser = $this->_userService->findByEmail("testadmin@thesisplanet.com");
		$this->_service->setUser($adminUser);

		$this->assertTrue($this->_service->acl_delete($courseId));
	}

	public function testUpdateCourse() {
		$courseData = array('topic' => "Computer Science", 'description' => "Course Testing", 'title' => "How to create unit tested software", 'isApprovalRequired' => 0, 'isSearchable' => 0, 'isEnabled' => 1, 'price' => 150);

		$user = $this->_userService->findByEmail("testprovider@thesisplanet.com");
		$this->_service->setUser($user);

		$courseId = $this->_service->acl_create($courseData);
		// Expect an integer
		$this->assertInternalType('integer', $courseId);

		$courseData['title'] = "Updating the title of the course";

		$this->assertTrue($this->_service->acl_update($courseId, $courseData));

		$adminUser = $this->_userService->findByEmail("testadmin@thesisplanet.com");
		$this->_service->setUser($adminUser);

		$this->assertTrue($this->_service->acl_delete($courseId));
	}

	public function testSubscribeUnsubscribeNoApproval() {
		$courseData = array('topic' => "Computer Science", 'description' => "Course Testing", 'title' => "How to create unit tested software", 'isApprovalRequired' => 0, 'isSearchable' => 0, 'isEnabled' => 1, 'price' => 150);

		$user = $this->_userService->findByEmail("testuser@thesisplanet.com");
		$this->_service->setUser($user);

		$courseId = $this->_service->acl_create($courseData);
		// Expect an integer
		$this->assertInternalType('integer', $courseId);

		$this->assertTrue($this->_service->acl_subscribe($courseId));

		$this->assertTrue($this->_service->acl_unsubscribe($courseId));

		$user = $this->_userService->findByEmail('testadmin@thesisplanet.com');
		$this->_service->setUser($user);

		$result = $this->_service->acl_delete($courseId);

		$this->assertTrue($result);
	}

	public function testGetDeleteForm() {
		$this->assertInstanceOf('App\Form\CourseDelete', $this->_service->getDeleteForm());
	}

	public function testGetForm() {
		$this->assertInstanceOf('App\Form\Course', $this->_service->getForm());
	}

	public function tearDown() {
		$this->_em = \Zend_Registry::get('em');
		$this->_em->clear();
		unset($this->_service);
		unset($this->_userService);
	}
}
