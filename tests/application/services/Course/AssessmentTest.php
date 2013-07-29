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
namespace tests\application\services\Course;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class AssessmentTest extends \Zend_Test_PHPUnit_ControllerTestCase
{

    protected $_service = null;

    protected $_userService = null;

    protected $_courseService = null;

    public function setUp ()
    {
        $this->_service = new \App\Service\Course\Assessment();
        $this->_userService = new \App\Service\User();
        $this->_courseService = new \App\Service\Course();
        $users = \Zend_Registry::getInstance()->get('users');
        $users->canCreateCourses = true;
    }

    public function testFindMethods ()
    {
        $user = $this->_userService->findByEmail(
                "testsubscriber@thesisplanet.com");
        $this->_service->setUser($user);
        
        $this->assertInternalType('array', $this->_service->acl_findByCourse(1));
    }

    public function testSetUserNonObject ()
    {
        $this->setExpectedException("exception", 
                \App\Service\Course::USER_NOT_OBJECT);
        $this->_service->setUser(null);
    }

    public function testACLNoUser ()
    {
        $this->setExpectedException("exception", 
                \App\Service\Course::USER_NOT_OBJECT);
        $this->_service->isAllowed(null, "acl_getThumbnailUrl");
    }

    public function testACLNotUserObject ()
    {
        $user = new \stdClass();
        
        $this->_service->setUser($user);
        
        $this->setExpectedException("exception", 
                \App\Service\Course::USER_MUST_IMPLEMENT_GETSUBSCRIPTIONS);
        $this->_service->isAllowed(null, "acl_getThumbnailUrl");
    }

    public function testACLNotValidProtectedSubject ()
    {
        $user = $this->_userService->findByEmail("testvisitor@thesisplanet.com");
        $this->_service->setUser($user);
        
        $subject = new \stdClass();
        
        $this->setExpectedException("exception", 
                \App\Service\Course::INVALID_PROTECTED_OBJECT);
        $this->_service->isAllowed($subject, "acl_getThumbnailUrl");
    }

    public function testACL ()
    {
        /**
         * Visitor
         */
        $user = $this->_userService->findByEmail("testvisitor@thesisplanet.com");
        $this->_service->setUser($user);
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_loadQuestions"));
        $this->assertFalse($this->_service->isAllowed(null, "acl_findByCourse"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_answerQuestion"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_takeAssessment"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_finishAttempt"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_fetchAttemptResults"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_getIsFinished"));
        $this->assertFalse($this->_service->isAllowed(null, "acl_findByCourse"));
        $this->assertFalse($this->_service->isAllowed(null, "acl_create"));
        $this->assertFalse($this->_service->isAllowed(null, "acl_delete"));
        $this->assertFalse(
                $this->_service->isAllowed(null, 
                        "acl_addMultipleChoiceQuestion"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_addTrueFalseQuestion"));
        $this->assertFalse(
                $this->_service->isAllowed(null, 
                        "acl_addFillInTheBlankQuestion"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_removeQuestion"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_setIsRetakeAllowed"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_findAllScores"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_findResultsByQuestion"));
        
        /**
         * Subscriber
         */
        $user = $this->_userService->findByEmail(
                "testsubscriber@thesisplanet.com");
        $this->_service->setUser($user);
        $this->assertTrue($this->_service->isAllowed(null, "acl_loadQuestions"));
        $this->assertTrue($this->_service->isAllowed(null, "acl_findByCourse"));
        $this->assertTrue(
                $this->_service->isAllowed(null, "acl_answerQuestion"));
        $this->assertTrue(
                $this->_service->isAllowed(null, "acl_takeAssessment"));
        $this->assertTrue($this->_service->isAllowed(null, "acl_finishAttempt"));
        $this->assertTrue(
                $this->_service->isAllowed(null, "acl_fetchAttemptResults"));
        $this->assertTrue($this->_service->isAllowed(null, "acl_getIsFinished"));
        $this->assertTrue($this->_service->isAllowed(null, "acl_findByCourse"));
        $this->assertFalse($this->_service->isAllowed(null, "acl_create"));
        $this->assertFalse($this->_service->isAllowed(null, "acl_delete"));
        $this->assertFalse(
                $this->_service->isAllowed(null, 
                        "acl_addMultipleChoiceQuestion"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_addTrueFalseQuestion"));
        $this->assertFalse(
                $this->_service->isAllowed(null, 
                        "acl_addFillInTheBlankQuestion"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_removeQuestion"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_setIsRetakeAllowed"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_findAllScores"));
        $this->assertFalse(
                $this->_service->isAllowed(null, "acl_findResultsByQuestion"));
        
        /**
         * Provider
         */
        $user = $this->_userService->findByEmail(
                "testprovider@thesisplanet.com");
        $this->_service->setUser($user);
        $this->assertTrue($this->_service->isAllowed(null, "acl_loadQuestions"));
        $this->assertTrue($this->_service->isAllowed(null, "acl_findByCourse"));
        $this->assertTrue(
                $this->_service->isAllowed(null, "acl_answerQuestion"));
        $this->assertTrue(
                $this->_service->isAllowed(null, "acl_takeAssessment"));
        $this->assertTrue($this->_service->isAllowed(null, "acl_finishAttempt"));
        $this->assertTrue(
                $this->_service->isAllowed(null, "acl_fetchAttemptResults"));
        $this->assertTrue($this->_service->isAllowed(null, "acl_getIsFinished"));
        $this->assertTrue($this->_service->isAllowed(null, "acl_findByCourse"));
        $this->assertTrue($this->_service->isAllowed(null, "acl_create"));
        $this->assertTrue($this->_service->isAllowed(null, "acl_delete"));
        $this->assertTrue(
                $this->_service->isAllowed(null, 
                        "acl_addMultipleChoiceQuestion"));
        $this->assertTrue(
                $this->_service->isAllowed(null, "acl_addTrueFalseQuestion"));
        $this->assertTrue(
                $this->_service->isAllowed(null, 
                        "acl_addFillInTheBlankQuestion"));
        $this->assertTrue(
                $this->_service->isAllowed(null, "acl_removeQuestion"));
        $this->assertTrue(
                $this->_service->isAllowed(null, "acl_setIsRetakeAllowed"));
        $this->assertTrue($this->_service->isAllowed(null, "acl_findAllScores"));
        $this->assertTrue(
                $this->_service->isAllowed(null, "acl_findResultsByQuestion"));
    }

    protected function createCourse ()
    {
        $courseData = array(
                'topic' => "Computer Science",
                'description' => "Course Testing",
                'title' => "How to create unit tested software",
                'isApprovalRequired' => 0,
                'isSearchable' => 0,
                'isEnabled' => 1,
                'price' => 150
        );
        
        $user = $this->_userService->findByEmail(
                "testsubscriber@thesisplanet.com");
        $service = new \App\Service\Course();
        $service->setUser($user);
        $courseId = $service->acl_create($courseData);
        return $courseId;
    }

    protected function deleteCourse ($courseId)
    {
        $user = $this->_userService->findByEmail("testadmin@thesisplanet.com");
        $service = new \App\Service\Course();
        $service->setUser($user);
        return $service->acl_delete($courseId);
    }

    public function testAclCreateAndDelete ()
    {
        $courseId = $this->createCourse();
        $user = $this->_userService->findByEmail(
                "testprovider@thesisplanet.com");
        $this->_service->setUser($user);
        $assessmentId = $this->_service->acl_create($courseId, 
                "Test Assessment", "This is a test assessment");
        $this->assertInternalType('integer', $assessmentId);
        $this->assertTrue($this->_service->acl_delete($assessmentId));
        $this->assertTrue($this->deleteCourse($courseId));
    }

    public function testAclAddMCQuestion ()
    {
        $courseId = $this->createCourse();
        $user = $this->_userService->findByEmail(
                "testprovider@thesisplanet.com");
        $this->_service->setUser($user);
        $assessmentId = $this->_service->acl_create($courseId, 
                "Test Assessment", "This is a test assessment");
        $this->assertInternalType('integer', $assessmentId);
        
        $questionId = $this->_service->acl_addMultipleChoiceQuestion(
                $assessmentId, "What is your favorite kind of tests?", 
                array(
                        0 => 'Unit tests',
                        1 => 'integration tests',
                        2 => 'difficult tests',
                        3 => 'Easy tests'
                ), 
                array(
                        0 => true,
                        1 => false,
                        2 => false,
                        3 => false
                ));
        $this->assertInternalType('integer', $questionId);
        $this->assertTrue($this->_service->acl_delete($assessmentId));
        $this->assertTrue($this->deleteCourse($courseId));
    }

    public function testAclAddTFQuestion ()
    {
        $courseId = $this->createCourse();
        $user = $this->_userService->findByEmail(
                "testprovider@thesisplanet.com");
        $this->_service->setUser($user);
        $assessmentId = $this->_service->acl_create($courseId, 
                "Test Assessment", "This is a test assessment");
        $this->assertInternalType('integer', $assessmentId);
        
        $questionId = $this->_service->acl_addTrueFalseQuestion($assessmentId, 
                "Unit testing is a best practice.", true);
        $this->assertInternalType('integer', $questionId);
        $this->assertTrue($this->_service->acl_delete($assessmentId));
        $this->assertTrue($this->deleteCourse($courseId));
    }

    public function testAclAddFIBQuestion ()
    {
        $courseId = $this->createCourse();
        $user = $this->_userService->findByEmail(
                "testprovider@thesisplanet.com");
        $this->_service->setUser($user);
        $assessmentId = $this->_service->acl_create($courseId, 
                "Test Assessment", "This is a test assessment");
        $this->assertInternalType('integer', $assessmentId);
        
        $questionId = $this->_service->acl_addFillInTheBlankQuestion(
                $assessmentId, "__unit__ testing is a good idea.");
        $this->assertInternalType('integer', $questionId);
        $this->assertTrue($this->_service->acl_delete($assessmentId));
        $this->assertTrue($this->deleteCourse($courseId));
    }

    public function testAclTakeAssessment ()
    {
        $courseId = $this->createCourse();
        $user = $this->_userService->findByEmail(
                "testprovider@thesisplanet.com");
        $this->_service->setUser($user);
        $this->_courseService->setUser($user);
        $assessmentId = $this->_service->acl_create($courseId, 
                "Test Assessment", "This is a test assessment");
        $this->assertInternalType('integer', $assessmentId);
        
        $questionId = $this->_service->acl_addMultipleChoiceQuestion(
                $assessmentId, "What is your favorite kind of tests?", 
                array(
                        0 => 'Unit tests',
                        1 => 'integration tests',
                        2 => 'difficult tests',
                        3 => 'Easy tests'
                ), 
                array(
                        0 => true,
                        1 => false,
                        2 => false,
                        3 => false
                ));
        $this->assertInternalType('integer', $questionId);
        
        // Subscribe to the course
        $this->_courseService->acl_subscribe($courseId);
        
        $user = $this->_userService->findByEmail(
                "testprovider@thesisplanet.com");
        $this->_service->setUser($user);
        $this->_courseService->setUser($user);
        
        $attemptId = $this->_service->acl_takeAssessment($assessmentId);
        $this->assertInternalType('integer', $attemptId);
        
        $this->assertTrue($this->_service->acl_removeQuestion($questionId));
        
        $this->assertTrue($this->_service->acl_delete($assessmentId));
        $this->_courseService->acl_unsubscribe($courseId);
        $this->assertTrue($this->deleteCourse($courseId));
    }

    public function testAclSetIsRetakeAllowed ()
    {
        $courseId = $this->createCourse();
        $user = $this->_userService->findByEmail(
                "testprovider@thesisplanet.com");
        $this->_service->setUser($user);
        $this->_courseService->setUser($user);
        $assessmentId = $this->_service->acl_create($courseId, 
                "Test Assessment", "This is a test assessment");
        $this->assertInternalType('integer', $assessmentId);
        $this->assertTrue(
                $this->_service->acl_setIsRetakeAllowed($assessmentId, true));
        $this->assertTrue($this->_service->acl_delete($assessmentId));
        $this->assertTrue($this->deleteCourse($courseId));
    }

    public function testAclGetIsFinishedForAnIncompleteAttempt ()
    {
        $courseId = $this->createCourse();
        $user = $this->_userService->findByEmail(
                "testprovider@thesisplanet.com");
        $this->_service->setUser($user);
        $this->_courseService->setUser($user);
        $assessmentId = $this->_service->acl_create($courseId, 
                "Test Assessment", "This is a test assessment");
        $this->assertInternalType('integer', $assessmentId);
        
        $questionId = $this->_service->acl_addMultipleChoiceQuestion(
                $assessmentId, "What is your favorite kind of tests?", 
                array(
                        0 => 'Unit tests',
                        1 => 'integration tests',
                        2 => 'difficult tests',
                        3 => 'Easy tests'
                ), 
                array(
                        0 => true,
                        1 => false,
                        2 => false,
                        3 => false
                ));
        $this->assertInternalType('integer', $questionId);
        
        // Subscribe to the course
        $this->_courseService->acl_subscribe($courseId);
        
        $user = $this->_userService->findByEmail(
                "testprovider@thesisplanet.com");
        $this->_service->setUser($user);
        $this->_courseService->setUser($user);
        $attemptId = $this->_service->acl_takeAssessment($assessmentId);
        $this->assertInternalType('integer', $attemptId);
        $this->assertFalse($this->_service->acl_getIsFinished($attemptId));
        $this->assertTrue($this->_service->acl_removeQuestion($questionId));
        $this->assertTrue($this->_service->acl_delete($assessmentId));
        $this->_courseService->acl_unsubscribe($courseId);
        $this->assertTrue($this->deleteCourse($courseId));
    }

    public function testAclFinishAttempt ()
    {}

    public function testScoringWorks ()
    {
        $user = $this->_userService->findByEmail(
                "testprovider@thesisplanet.com");
        $courseId = $this->createCourse();
        $this->_courseService->setUser($user);
        $this->_courseService->acl_subscribe($courseId);
        $user = $this->_userService->findByEmail(
                "testprovider@thesisplanet.com");
        $this->_service->setUser($user);
        $assessmentId = $this->_service->acl_create($courseId, 
                "Test Assessment", "This is a test assessment");
        $this->assertInternalType('integer', $assessmentId);
        
        // Add questions
        $questionId = $this->_service->acl_addTrueFalseQuestion($assessmentId, 
                "Unit testing is a best practice.", true);
        $this->assertInternalType('integer', $questionId);
        
        $questionId = $this->_service->acl_addMultipleChoiceQuestion(
                $assessmentId, "What is your favorite kind of tests?", 
                array(
                        0 => 'Unit tests',
                        1 => 'integration tests',
                        2 => 'difficult tests',
                        3 => 'Easy tests'
                ), 
                array(
                        0 => true,
                        1 => false,
                        2 => false,
                        3 => false
                ));
        $this->assertInternalType('integer', $questionId);
        
        // Make an attempt
        
        $attemptId = $this->_service->acl_takeAssessment($assessmentId);
        $this->assertInternalType('integer', $attemptId);
        
        // Load questions
        
        $questions = $this->_service->acl_fetchQuestions($attemptId);
        // $this->assertInternalType('array', $questions);
        
        // Answer questions in attempt
        foreach ($questions as $questionNumber => $questionObjAsArr) {
            $questionType = $questionObjAsArr['class'];
            switch ($questionType) {
                case 'App\Entity\Course\Assessment\Question\TrueFalse':
                    $answer = true;
                    
                    break;
                case 'App\Entity\Course\Assessment\Question\MultipleChoice':
                    $answer = array(
                            0 => true,
                            1 => false,
                            2 => false,
                            3 => false
                    );
                    break;
                default:
                    $answer = null;
            }
            
            $this->assertTrue(
                    $this->_service->acl_answerQuestion($attemptId, 
                            $questionNumber, $answer));
        }
        
        // Score assessment
        $this->assertTrue($this->_service->acl_finishAttempt($attemptId));
        $this->assertTrue($this->_service->acl_getIsFinished($attemptId));
        
        $this->assertEquals(1, 
                $this->_service->acl_fetchAttemptResults($attemptId));
        
        // Delete assessment
        $this->assertTrue($this->_service->acl_delete($assessmentId));
        
        // Delete course
        $this->assertTrue($this->deleteCourse($courseId));
    }

    public function tearDown ()
    {
        $this->_em = \Zend_Registry::get('em');
        $this->_em->clear();
        unset($this->_service);
        unset($this->_userService);
        unset($this->_courseService);
    }
}