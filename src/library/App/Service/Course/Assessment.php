<?php
namespace App\Service\Course;

class Assessment extends \App\Service\Base implements iAssessment
{

    protected $_repository = null;

    protected $_courseRepository = null;

    protected $_questionRepository = null;

    protected $_attemptRepository = null;

    protected $_form = null;

    protected $_deleteForm = null;

    protected $_user = null;

    const USER_MUST_IMPLEMENT_GETSUBSCRIPTIONS = "The user object must provide the getSubscriptions method.";

    const PERMISSION_DENIED = "You do not have permission to perform that action.";

    const COURSE_NOT_FOUND = "That course was not found.";

    const ASSESSMENT_NOT_FOUND = "That assessment does not exist.";

    const ATTEMPT_NOT_FOUND = "That attempt does not exist.";

    const EXPECTS_ARRAY = "An array of information must be provided.";

    const QUESTION_NOT_FOUND = "Unable to find that question.";

    const SUBSCRIPTION_NOT_FOUND = "Unable to find that subscription";

    const NO_SUBSCRIPTIONS_FOUND = "Unable to locate any subscriptions.";

    const NO_QUESTIONS_IN_ASSESSMENT = "There must first be questions in an assessment.";

    const BOOLEAN_NOT_PROVIDED = "A true or false value must be provided.";

    const ATTEMPT_QUESTION_NOT_FOUND = "Unable to locate that question in your assessment attempt.";

    const ATTEMPT_ALREADY_FINISHED = "That attempt was already marked as complete.";

    const FIB_MUST_CONTAIN_AT_LEAST_ONE_WORD = "Fill in the blank questions must contain at least one blank word to match. __blank__ is what a blank word looks like (two underscores before and after the word)";

    public function __construct ($options = array())
    {
        $this->_em = \Zend_Registry::get('em');
        $this->_repository = $this->_em->getRepository(
                '\App\Entity\Course\Assessment');
        $this->_courseRepository = $this->_em->getRepository(
                '\App\Entity\Course');
        $this->_questionRepository = $this->_em->getRepository(
                '\App\Entity\Course\Assessment\Question');
        $this->_attemptRepository = $this->_em->getRepository(
                '\App\Entity\Course\Assessment\Attempt');
        $this->_acl = new \App\Service\ACL\Course\Assessment();
    }

    public function isAllowed ($courseObj, $action)
    {
        if (! is_object($this->_user)) {
            throw new \exception(self::USER_NOT_OBJECT);
        }
        if (! method_exists($this->_user, 'getSubscriptions')) {
            throw new \exception(self::USER_MUST_IMPLEMENT_GETSUBSCRIPTIONS);
        }
        $userService = new \App\Service\User();
        if (! is_object($courseObj)) {
            $role = $userService->authorize($this->_user->getId());
            if ($this->_acl->isAllowed($role, null, $action)) {
                return true;
            } else {
                return false;
            }
        } else {
            if (! method_exists($courseObj, 'getId')) {
                throw new \exception(self::INVALID_PROTECTED_OBJECT);
            }
            $subs = $this->_user->getsubscriptions();
            foreach ($subs as $key => $subscriptionObject) {
                if ($subscriptionObject->getCourse()->getId() ===
                         $courseObj->getId()) {
                    if ($this->_acl->isAllowed($subscriptionObject->getRole(), 
                            null, $action)) {
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
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_create()
     */
    public function acl_create ($courseId, $title, $description)
    {
        $logger = \Zend_Registry::get('logger');
        $logger->log(
                "creating assessment: courseID: $courseId, title: $title, description: $description", 
                \Zend_Log::INFO);
        $courseObj = $this->_courseRepository->find($courseId);
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        $textFilter = new \Zend_Filter_HtmlEntities();
        $assessmentObj = new \App\Entity\Course\Assessment();
        $assessmentObj->setCourse($courseObj);
        $assessmentObj->setCreatedAt(new \DateTime());
        $assessmentObj->setIsRetakeAllowed(false);
        $assessmentObj->setDescription($textFilter->filter($description));
        $assessmentObj->setTitle($textFilter->filter($title));
        $this->_em->persist($assessmentObj);
        $this->_em->flush();
        return $assessmentObj->getId();
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_delete()
     */
    public function acl_delete ($assessmentId)
    {
        $assessmentObj = $this->_repository->find($assessmentId);
        if (! is_object($assessmentObj)) {
            throw new \exception(self::ASSESSMENT_NOT_FOUND);
        }
        $courseObj = $assessmentObj->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        foreach ($assessmentObj->getQuestions() as $questionObj) {
            $this->_em->remove($questionObj);
        }
        foreach ($assessmentObj->getAttempts() as $attemptObj) {
            $this->_em->remove($attemptObj);
        }
        
        // remove the entity from the entity manager.
        $this->_em->remove($assessmentObj);
        
        // delete from the database.
        $this->_em->flush();
        return true;
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Course\iAssessment::acl_addMultipleChoiceQuestion()
     */
    public function acl_addMultipleChoiceQuestion ($assessmentId, $questionText, 
            $possibleAnswersArr, $correctArr)
    {
        // check if allowed to create a new question
        $assessmentObj = $this->_repository->find($assessmentId);
        if (! is_object($assessmentObj)) {
            throw new \exception(self::ASSESSMENT_NOT_FOUND);
        }
        $courseObj = $assessmentObj->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        $textFilter = new \Zend_Filter_HtmlEntities();
        
        // Filter the array of inputs.
        if (! is_array($possibleAnswersArr)) {
            throw new \exception(self::EXPECTS_ARRAY . " - Questions");
        }
        $possibleAnswersArrFiltered = array();
        foreach ($possibleAnswersArr as $key => $value) {
            $possibleAnswersArrFiltered[] = $textFilter->filter($value);
        }
        if (! is_array($correctArr)) {
            throw new \exception(self::EXPECTS_ARRAY . " - Correct Answers");
        }
        $correctArrFiltered = array();
        foreach ($correctArr as $key => $value) {
            $correctArrFiltered[$textFilter->filter($key)] = $textFilter->filter(
                    $value);
        }
        // Save the question
        
        $questionObj = new \App\Entity\Course\Assessment\Question\MultipleChoice();
        $questionObj->setAssessment($assessmentObj);
        $questionObj->setPossibleAnswers($possibleAnswersArrFiltered);
        $questionObj->setCorrectAnswers($correctArrFiltered);
        $questionObj->setTitle($textFilter->filter($questionText));
        $assessmentObj->getQuestions()->add($questionObj);
        $this->_em->persist($assessmentObj);
        $this->_em->persist($questionObj);
        $this->_em->flush();
        
        return $questionObj->getId();
    }

    public function acl_updateMultipleChoiceQuestion ($questionId, $questionText, 
            $possibleAnswersArr, $correctArr)
    {
        // check if allowed to create a new question
        $questionObj = $this->_questionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        }
        $assessmentObj = $questionObj->getAssessment();
        
        $courseObj = $assessmentObj->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        $textFilter = new \Zend_Filter_HtmlEntities();
        
        // Filter the array of inputs.
        if (! is_array($possibleAnswersArr)) {
            throw new \exception(self::EXPECTS_ARRAY . " - Questions");
        }
        $possibleAnswersArrFiltered = array();
        foreach ($possibleAnswersArr as $key => $value) {
            $possibleAnswersArrFiltered[] = $textFilter->filter($value);
        }
        if (! is_array($correctArr)) {
            throw new \exception(self::EXPECTS_ARRAY . " - Correct Answers");
        }
        $correctArrFiltered = array();
        foreach ($correctArr as $key => $value) {
            $correctArrFiltered[$textFilter->filter($key)] = $textFilter->filter(
                    $value);
        }
        $questionObj->setAssessment($assessmentObj);
        $questionObj->setPossibleAnswers($possibleAnswersArrFiltered);
        $questionObj->setCorrectAnswers($correctArrFiltered);
        $questionObj->setTitle($textFilter->filter($questionText));
        $assessmentObj->getQuestions()->add($questionObj);
        $this->_em->persist($assessmentObj);
        $this->_em->persist($questionObj);
        $this->_em->flush();
        
        return true;
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Course\iAssessment::acl_addTrueFalseQuestion()
     */
    public function acl_addTrueFalseQuestion ($assessmentId, $questionString, 
            $answerBoolean)
    {
        // check if allowed to create a new question
        $assessmentObj = $this->_repository->find($assessmentId);
        if (! is_object($assessmentObj)) {
            throw new \exception(self::ASSESSMENT_NOT_FOUND);
        }
        $courseObj = $assessmentObj->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        $textFilter = new \Zend_Filter_HtmlEntities();
        // Save the question
        
        $questionObj = new \App\Entity\Course\Assessment\Question\TrueFalse();
        $questionObj->setAssessment($assessmentObj);
        $questionObj->setTitle($textFilter->filter($questionString));
        $questionObj->setAnswer($answerBoolean);
        $assessmentObj->getQuestions()->add($questionObj);
        $this->_em->persist($assessmentObj);
        $this->_em->persist($questionObj);
        $this->_em->flush();
        
        return $questionObj->getId();
    }

    public function acl_updateTrueFalseQuestion ($questionId, $questionString, 
            $answerBoolean)
    {
        // check if allowed to create a new question
        $questionObj = $this->_questionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        }
        $assessmentObj = $questionObj->getAssessment();
        $courseObj = $assessmentObj->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        $textFilter = new \Zend_Filter_HtmlEntities();
        // Save the question
        
        $questionObj->setAssessment($assessmentObj);
        $questionObj->setTitle($textFilter->filter($questionString));
        $questionObj->setAnswer($answerBoolean);
        $assessmentObj->getQuestions()->add($questionObj);
        $this->_em->persist($assessmentObj);
        $this->_em->persist($questionObj);
        $this->_em->flush();
        
        return true;
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Course\iAssessment::acl_addFillInTheBlankQuestion()
     */
    public function acl_addFillInTheBlankQuestion ($assessmentId, 
            $questionString)
    {
        // check if allowed to create a new question
        $assessmentObj = $this->_repository->find($assessmentId);
        if (! is_object($assessmentObj)) {
            throw new \exception(self::ASSESSMENT_NOT_FOUND);
        }
        $courseObj = $assessmentObj->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        
        // Save the question
        
        $questionObj = new \App\Entity\Course\Assessment\Question\FillInTheBlank();
        $questionObj->setAssessment($assessmentObj);
        $questionObj->setTitle(htmlspecialchars($questionString));
        $assessmentObj->getQuestions()->add($questionObj);
        $correctWordsArr = array();
        preg_match_all('/__[a-zA-Z0-9]+__/', $questionString, $correctWordsArr);
        if (count($correctWordsArr) == 0) {
            throw new \exception(self::FIB_MUST_CONTAIN_AT_LEAST_ONE_WORD);
        }
        if (is_array($correctWordsArr))
            foreach ($correctWordsArr as $key => $word) {
                $correctWordsArr[$key] = preg_filter("/_/", "", $word);
            }
        $correctWordsArr = $correctWordsArr[0];
        $questionObj->setCorrectAnswers($correctWordsArr);
        
        $questionObj->setTitle(
                preg_replace_callback('/__[a-zA-Z0-9]+__/', 
                        function  ($input) use( $questionString)
                        {
                            return "__blank__";
                        }, $questionString));
        
        $this->_em->persist($assessmentObj);
        $this->_em->persist($questionObj);
        $this->_em->flush();
        
        return $questionObj->getId();
    }

    public function acl_updateFillInTheBlankQuestion ($questionId, 
            $questionString)
    {
        // check if allowed to create a new question
        $questionObj = $this->_questionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        }
        $assessmentObj = $questionObj->getAssessment();
        $courseObj = $assessmentObj->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        
        $questionObj->setAssessment($assessmentObj);
        $questionObj->setTitle(htmlspecialchars($questionString));
        $assessmentObj->getQuestions()->add($questionObj);
        
        $correctWordsArr = array();
        preg_match_all('/__[a-zA-Z0-9]+__/', $questionString, $correctWordsArr);
        if (count($correctWordsArr) == 0) {
            throw new \exception(self::FIB_MUST_CONTAIN_AT_LEAST_ONE_WORD);
        }
        if (is_array($correctWordsArr))
            foreach ($correctWordsArr as $key => $word) {
                $correctWordsArr[$key] = preg_filter("/_/", "", $word);
            }
        
        $correctWordsArr = $correctWordsArr[0];
        $questionObj->setCorrectAnswers($correctWordsArr);
        
        $questionObj->setTitle(
                preg_replace_callback('/__[a-zA-Z0-9]+__/', 
                        function  ($input) use( $questionString)
                        {
                            return "__blank__";
                        }, $questionString));
        
        $this->_em->persist($assessmentObj);
        $this->_em->persist($questionObj);
        $this->_em->flush();
        
        return true;
    }

    public function acl_find ($assessmentId)
    {
        $assessmentObj = $this->_repository->find($assessmentId);
        if (! is_object($assessmentObj)) {
            throw new \exception(self::ASSESSMENT_NOT_FOUND);
        }
        
        // check if allowed to create a new question
        $courseObj = $assessmentObj->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        
        return $assessmentObj;
    }

    /**
     *
     * @see \App\Service\Course\iAssessment::acl_findQuestion()
     */
    public function acl_findQuestion ($questionId)
    
    {
        $questionObj = $this->_questionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        } else {
            $courseObj = $questionObj->getAssessment()->getCourse();
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
            return $questionObj;
        }
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_findByCourse()
     */
    public function acl_findByCourse ($courseId)
    {
        // check if allowed to create a new question
        $courseObj = $this->_courseRepository->find($courseId);
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        $assessments = $this->_repository->findBy(
                array(
                        '_course' => $courseObj
                ));
        return $assessments;
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_removeQuestion()
     */
    public function acl_removeQuestion ($questionId)
    {
        $questionObj = $this->_questionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        }
        $assessmentObj = $questionObj->getAssessment();
        if (! is_object($assessmentObj)) {
            throw new \exception(self::ASSESSMENT_NOT_FOUND);
        }
        $courseObj = $assessmentObj->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        $this->_em->remove($questionObj);
        $this->_em->flush();
        return true;
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_answerQuestion()
     */
    public function acl_answerQuestion ($attemptId, $questionId, $answer)
    {
        // check if allowed to take an assessment
        $attemptObj = $this->_attemptRepository->find($attemptId);
        if (! is_object($attemptObj)) {
            throw new \exception(self::ATTEMPT_NOT_FOUND);
        }
        $courseObj = $attemptObj->getAssessment()->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        
        // locate the question.
        $questions = $attemptObj->getQuestions();
        if (! array_key_exists($questionId, $questions)) {
            throw new \exception(self::ATTEMPT_QUESTION_NOT_FOUND);
        }
        $answers = $attemptObj->getAnswers();
        if (! is_array($answers)) {
            $answers = array();
        }
        if (is_array($answer)) {
            foreach ($answer as $key => $value) {
                $answer[$key] = (string) $value;
            }
        }
        $answers[$questionId] = $answer;
        
        $attemptObj->setAnswers($answers);
        $this->_em->persist($attemptObj);
        $this->_em->flush();
        return true;
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_takeAssessment()
     */
    public function acl_takeAssessment ($assessmentId)
    {
        // check if allowed to take an assessment
        $assessmentObj = $this->_repository->find($assessmentId);
        if (! is_object($assessmentObj)) {
            throw new \exception(self::ASSESSMENT_NOT_FOUND);
        }
        $courseObj = $assessmentObj->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        
        // Locate subscription Object
        $subscriptions = $this->_user->getSubscriptions();
        foreach ($subscriptions as $tempSubscriptionObj) {
            if ($tempSubscriptionObj->getCourse()->getId() == $courseObj->getId()) {
                $subscriptionObj = $tempSubscriptionObj;
            }
        }
        if (! is_object($subscriptionObj)) {
            throw new \exception(self::SUBSCRIPTION_NOT_FOUND);
        }
        
        // 1. Create a new attempt object
        
        $attemptObj = new \App\Entity\Course\Assessment\Attempt();
        $attemptObj->setStartedAt(new \DateTime());
        $attemptObj->setAssessment($assessmentObj);
        $attemptObj->setSubscription($subscriptionObj);
        // locate the current subscriptionObj
        
        // 2. Load in all of the questions and randomize them.
        $questions = $assessmentObj->getQuestions();
        
        if (count($questions) > 0) {} else {
            throw new \exception(self::NO_QUESTIONS_IN_ASSESSMENT);
        }
        $mixedQuestionsArr = array();
        foreach ($questions as $questionObj) {
            $mixedQuestionsArr[] = $questionObj->toArray();
        }
        shuffle($mixedQuestionsArr);
        $attemptObj->setQuestions($mixedQuestionsArr);
        $attemptObj->setAnswers(array());
        $assessmentObj->getAttempts()->add($attemptObj);
        $this->_em->persist($assessmentObj);
        $this->_em->persist($attemptObj);
        $this->_em->flush();
        return $attemptObj->getId();
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Course\iAssessment::acl_setIsRetakeAllowed()
     */
    public function acl_setIsRetakeAllowed ($assessmentId, $allowRetakesBoolean)
    {
        // check if allowed to take an assessment
        $assessmentObj = $this->_repository->find($assessmentId);
        if (! is_object($assessmentObj)) {
            throw new \exception(self::ASSESSMENT_NOT_FOUND);
        }
        $courseObj = $assessmentObj->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        if (! is_bool($allowRetakesBoolean)) {
            throw new \exception(self::BOOLEAN_NOT_PROVIDED);
        }
        
        $assessmentObj->setIsRetakeAllowed($allowRetakesBoolean);
        $this->_em->persist($assessmentObj);
        $this->_em->flush();
        return true;
    }

    public function acl_findAttempt ($attemptId)
    {
        $attemptObj = $this->_attemptRepository->find($attemptId);
        if (! is_object($attemptObj)) {
            throw new \exception(self::ATTEMPT_NOT_FOUND);
        }
        $courseObj = $attemptObj->getAssessment()->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        
        return $attemptObj;
    }

    public function acl_listAttempts ($assessmentId)
    {
        $assessmentObj = $this->_repository->find($assessmentId);
        
        if (! is_object($assessmentObj)) {
            throw new \exception(self::ASSESSMENT_NOT_FOUND);
        }
        $courseObj = $assessmentObj->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        
        $subscriptions = $this->_user->getSubscriptions();
        $activeSubscription = null;
        foreach ($subscriptions as $key => $subscriptionObj) {
            if ($subscriptionObj->getCourse()->getId() ==
                     $assessmentObj->getCourse()->getId()) {
                $activeSubscription = $subscriptionObj;
            }
        }
        
        $attempts = $this->_attemptRepository->findBy(
                array(
                        '_assessment' => $assessmentObj,
                        '_subscription' => $activeSubscription
                ));
        
        return $attempts;
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_fetchQuestions()
     */
    public function acl_fetchQuestions ($attemptId)
    {
        // check if allowed to take an assessment
        $attemptObj = $this->_attemptRepository->find($attemptId);
        if (! is_object($attemptObj)) {
            throw new \exception(self::ATTEMPT_NOT_FOUND);
        }
        $courseObj = $attemptObj->getAssessment()->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        
        $questions = $attemptObj->getQuestions();
        $questionsOut = array();
        foreach ($questions as $key => $question) {
            $questionsOut[$key] = $question;
            unset($questionsOut[$key]['correctAnswers']);
            unset($questionsOut[$key]['answer']);
        }
        
        return $questionsOut;
    }

    public function acl_fetchSubmittedAnswers ($attemptId)
    {
        // check if allowed to take an assessment
        $attemptObj = $this->_attemptRepository->find($attemptId);
        if (! is_object($attemptObj)) {
            throw new \exception(self::ATTEMPT_NOT_FOUND);
        }
        $courseObj = $attemptObj->getAssessment()->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        
        return $attemptObj->getAnswers();
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_finishAttempt()
     */
    public function acl_finishAttempt ($attemptId)
    {
        // 1. load it
        $attemptObj = $this->_attemptRepository->find($attemptId);
        if (! is_object($attemptObj)) {
            throw new \exception(self::ATTEMPT_NOT_FOUND);
        }
        $courseObj = $attemptObj->getAssessment()->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        if ($attemptObj->getFinishedAt() instanceof \DateTime) {
            throw new \exception(self::ATTEMPT_ALREADY_FINISHED);
        }
        $logger = \Zend_Registry::get('logger');
        // 2. score it.
        $questions = $attemptObj->getQuestions();
        $answers = $attemptObj->getAnswers();
        $correct = array();
        foreach ($attemptObj->getQuestions() as $questionNumber => $questionObjAsArr) {
            if (! array_key_exists($questionNumber, $answers)) {
                $correct[$questionNumber] = false;
            }
            
            // OK, let's assume an answer of some kind was submitted.
            switch ($questionObjAsArr['class']) {
                case 'App\Entity\Course\Assessment\Question\TrueFalse':
                    if ($questionObjAsArr['answer'] == $answers[$questionNumber]) {
                        $correct[$questionNumber] = true;
                        $logger->log(
                                "Attempt ID: $attemptId T/F Question# $questionNumber is correct.", 
                                \Zend_Log::INFO);
                    } else {
                        $logger->log(
                                "Attempt ID: $attemptId T/F Question# $questionNumber is INCORRECT.", 
                                \Zend_Log::INFO);
                        $correct[$questionNumber] = false;
                    }
                    break;
                case 'App\Entity\Course\Assessment\Question\MultipleChoice':
                    // check that the arrays match
                    
                    $differences = array_diff_uassoc(
                            $questionObjAsArr['correctAnswers'], 
                            $answers[$questionNumber], 
                            function  ($a, $b)
                            {
                                if ($a == $b) {
                                    return 0;
                                }
                                return ($a > $b) ? 1 : - 1;
                            });
                    if (count($differences) > 0) {
                        $logger->log(
                                "Attempt ID: $attemptId M/C Question# $questionNumber is INCORRECT:" .
                                         var_export($differences, true), 
                                        \Zend_Log::INFO);
                        $correct[$questionNumber] = false;
                    } else {
                        $logger->log(
                                "Attempt ID: $attemptId M/C Question# $questionNumber is CORRECT.", 
                                \Zend_Log::INFO);
                        $correct[$questionNumber] = true;
                    }
                    break;
                case 'App\Entity\Course\Assessment\Question\FillInTheBlank':
                    
                    // correct answers should be parsed first, then saved back.
                    $differences = array_diff_uassoc(
                            $questionObjAsArr['correctAnswers'], 
                            $answers[$questionNumber], 
                            function  ($a, $b)
                            {
                                if ($a == $b) {
                                    return 0;
                                }
                                return ($a > $b) ? 1 : - 1;
                            });
                    if (count($differences) > 0) {
                        $logger->log(
                                "Attempt ID: $attemptId F/I/B Question# $questionNumber is INCORRECT:" .
                                         var_export($differences, true), 
                                        \Zend_Log::INFO);
                        $correct[$questionNumber] = false;
                    } else {
                        $logger->log(
                                "Attempt ID: $attemptId F/I/B Question# $questionNumber is CORRECT.", 
                                \Zend_Log::INFO);
                        $correct[$questionNumber] = true;
                    }
                    
                    break;
                default:
                    var_dump($questionObjAsArr);
            }
        }
        // 3. lock it.
        
        $attemptObj->setFinishedAt(new \DateTime());
        $attemptObj->setCorrected($correct);
        $totalQuestions = 0;
        $sumOfCorrect = 0;
        
        foreach ($correct as $key => $correctness) {
            $totalQuestions ++;
            if ($correctness == true) {
                $sumOfCorrect ++;
            }
        }
        if ($totalQuestions != 0) {
            $attemptObj->setScore(floatval($sumOfCorrect / $totalQuestions));
        } else {
            $attemptObj->setScore(0);
        }
        $this->_em->persist($attemptObj);
        $this->_em->flush();
        return true;
        // 4. notify about it.
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Course\iAssessment::acl_fetchAttemptResults()
     */
    public function acl_fetchAttemptResults ($attemptId)
    {
        // check if allowed to take an assessment
        $attemptObj = $this->_attemptRepository->find($attemptId);
        if (! is_object($attemptObj)) {
            throw new \exception(self::ATTEMPT_NOT_FOUND);
        }
        $courseObj = $attemptObj->getAssessment()->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        return $attemptObj->getScore();
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_findAllScores()
     */
    public function acl_findAllScores ($assessmentId)
    {}
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Course\iAssessment::acl_findResultsByQuestion()
     */
    public function acl_findResultsByQuestion ($questionId)
    {
        // TODO Auto-generated method stub
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_getIsFinished()
     */
    public function acl_getIsFinished ($attemptId)
    {
        // check if allowed to take an assessment
        $attemptObj = $this->_attemptRepository->find($attemptId);
        if (! is_object($attemptObj)) {
            throw new \exception(self::ATTEMPT_NOT_FOUND);
        }
        $courseObj = $attemptObj->getAssessment()->getCourse();
        if (! is_object($courseObj)) {
            throw new \exception(self::COURSE_NOT_FOUND);
        } else {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        }
        if ($attemptObj->getFinishedAt() instanceof \DateTime) {
            return true;
        } else {
            return false;
        }
    }
}
