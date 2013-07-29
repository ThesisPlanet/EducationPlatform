<?php

class Site_AssessmentController extends Zend_Controller_Action
{

    public function init ()
    {
        $this->_service = new \App\Service\Course\Assessment();
        if (\Zend_Auth::getInstance()->hasIdentity()) {
            $this->_service->setUser(
                    \Zend_Auth::getInstance()->getIdentity()
                        ->getUser());
        }
        $this->view->service = $this->_service;
    }

    public function indexAction ()
    {
        $list = $this->_service->acl_findByCourse(
                $this->getRequest()
                    ->getParam('id'));
        $this->view->assessmentList = $list;
        $this->view->courseId = $this->_request->getParam('id');
    }

    public function editAction ()
    {
        $assessmentObj = $this->_service->acl_find(
                $this->_request->getParam('id'));
        $this->view->assessment = $assessmentObj;
        $this->view->course = $assessmentObj->getCourse();
    }

    public function viewAction ()
    {
        $assessmentObj = $this->_service->acl_find(
                $this->_request->getParam('id'));
        $this->view->assessment = $assessmentObj;
        $attempts = $this->_service->acl_listAttempts($assessmentObj->getId());
        $this->view->attempts = $attempts;
        $scores = array();
        $highScore = 0;
        foreach ($attempts as $attempt) {
            if ($attempt->getFinishedAt() instanceof \DateTime) {
                $scores[] = $attempt->getScore();
                if ($attempt->getScore() >= $highScore)
                    $highScore = $attempt->getScore();
            }
        }
        if (count($scores) == 0) {
            $this->view->averageScore = 0;
        } else {
            $this->view->averageScore = (array_sum($scores) / count($scores));
        }
        
        $this->view->highScore = $highScore;
        $this->view->course = $assessmentObj->getCourse();
    }

    public function addAction ()
    {
        $courseId = $this->_request->getParam('id');
        $form = new \App\Form\Course\Assessment();
        
        if ($this->_request->isPost()) {
            $assessmentId = $this->_service->acl_create($courseId, 
                    $this->_request->getParam('title'), 
                    $this->_request->getParam('description'));
            if ($assessmentId) {
                $this->_helper->flashMessenger($this->_service->getMessage());
                $this->_redirect('/assessment/edit/' . $assessmentId);
            }
        }
        $this->view->courseId = $courseId;
        $this->view->form = $form;
    }

    public function deleteAction ()
    {
        $assessmentObj = $this->_service->acl_find(
                $this->_request->getParam('id'));
        $this->view->assessment = $assessmentObj;
        $courseId = $assessmentObj->getCourse()->getId();
        $form = new \App\Form\Course\Assessment\Delete();
        if ($this->_request->isPost()) {
            $courseId = $assessmentObj->getCourse()->getId();
            if ($this->_service->acl_delete($assessmentObj->getId())) {
                $this->_helper->flashMessenger($this->_service->getMessage());
                $this->_redirect('/course/curriculum/' . $courseId);
            }
        }
        
        $this->view->form = $form;
    }

    public function addtfAction ()
    {
        $assessmentObj = $this->_service->acl_find(
                $this->_request->getParam('id'));
        $this->view->assessment = $assessmentObj;
        $form = new \App\Form\Course\Assessment\Question\TrueFalse();
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {
                $question = $this->_service->acl_addTrueFalseQuestion(
                        $assessmentObj->getId(), $form->getValue("question"), 
                        $form->getValue("answer"));
                if (is_int($question)) {
                    $this->_redirect(
                            "/assessment/edit/" . $assessmentObj->getId());
                }
            }
        }
        
        $this->view->form = $form;
    }

    public function addmcAction ()
    {
        $assessmentObj = $this->_service->acl_find(
                $this->_request->getParam('id'));
        $this->view->assessment = $assessmentObj;
        $form = new \App\Form\Course\Assessment\Question\MultipleChoice();
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {
                $possibleAnswersArr = array(
                        0 => $form->getValue("answer_1"),
                        1 => $form->getValue("answer_2"),
                        2 => $form->getValue("answer_3"),
                        3 => $form->getValue("answer_4")
                );
                $correctArr = array(
                        0 => $form->getValue("answer_1_correct"),
                        1 => $form->getValue("answer_2_correct"),
                        2 => $form->getValue("answer_3_correct"),
                        3 => $form->getValue("answer_4_correct")
                );
                
                $question = $this->_service->acl_addMultipleChoiceQuestion(
                        $assessmentObj->getId(), $form->getValue("question"), 
                        $possibleAnswersArr, $correctArr);
                
                if (is_int($question)) {
                    $this->_redirect(
                            "/assessment/edit/" . $assessmentObj->getId());
                }
            }
        }
        
        $this->view->form = $form;
    }

    public function addfibAction ()
    {
        $assessmentObj = $this->_service->acl_find(
                $this->_request->getParam('id'));
        $this->view->assessment = $assessmentObj;
        $form = new \App\Form\Course\Assessment\Question\FillInTheBlank();
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {
                $question = $this->_service->acl_addFillInTheBlankQuestion(
                        $assessmentObj->getId(), $form->getValue("question"));
                if (is_int($question)) {
                    $this->_redirect(
                            "/assessment/edit/" . $assessmentObj->getId());
                }
            }
        }
        
        $this->view->form = $form;
    }

    public function editQuestionAction ()
    {
        $questionObj = $this->_service->acl_findQuestion(
                $this->_request->getParam('id'));
        if (! is_object($questionObj)) {
            throw new \exception("No question object found.");
        }
        switch ($questionObj->getClass()) {
            case 'App\Entity\Course\Assessment\Question\TrueFalse':
                $form = new \App\Form\Course\Assessment\Question\TrueFalse();
                $data = array(
                        'question' => $questionObj->getTitle(),
                        'answer' => $questionObj->getAnswer()
                );
                $form->populate($data);
                
                if ($this->_request->isPost()) {
                    $receivedData = $this->_request->getParams();
                    
                    if ($form->isValid($receivedData)) {
                        $response = $this->_service->acl_updateTrueFalseQuestion(
                                $questionObj->getId(), 
                                $form->getValue('question'), 
                                $form->getValue('answer'));
                    }
                }
                
                break;
            case 'App\Entity\Course\Assessment\Question\MultipleChoice':
                $form = new \App\Form\Course\Assessment\Question\MultipleChoice();
                
                $possibleAnswers = $questionObj->getPossibleAnswers();
                $correctAnswers = $questionObj->getCorrectAnswers();
                $data = array(
                        'question' => $questionObj->getTitle(),
                        'answer_1' => $possibleAnswers[0],
                        'answer_2' => $possibleAnswers[1],
                        'answer_3' => $possibleAnswers[2],
                        'answer_4' => $possibleAnswers[3],
                        'answer_1_correct' => $correctAnswers[0],
                        'answer_2_correct' => $correctAnswers[1],
                        'answer_3_correct' => $correctAnswers[2],
                        'answer_4_correct' => $correctAnswers[3]
                );
                $form->populate($data);
                if ($this->_request->isPost()) {
                    $receivedData = $this->_request->getParams();
                    
                    if ($form->isValid($receivedData)) {
                        $possibleAnswersArr = array(
                                0 => $form->getValue('answer_1'),
                                1 => $form->getValue('answer_2'),
                                2 => $form->getValue('answer_3'),
                                3 => $form->getValue('answer_4')
                        );
                        $correctAnswersArr = array(
                                0 => $form->getValue('answer_1_correct'),
                                1 => $form->getValue('answer_2_correct'),
                                2 => $form->getValue('answer_3_correct'),
                                3 => $form->getValue('answer_4_correct')
                        );
                        
                        $this->_service->acl_updateMultipleChoiceQuestion(
                                $questionObj->getId(), 
                                $form->getValue('question'), $possibleAnswersArr, 
                                $correctAnswersArr);
                    }
                }
                break;
            case 'App\Entity\Course\Assessment\Question\FillInTheBlank':
                $form = new \App\Form\Course\Assessment\Question\FillInTheBlank();
                
                // replace the __blank__ with the correct item from
                $correctAnswersArr = $questionObj->getCorrectAnswers();
                // correctAnswers arr.
                $questionString = preg_replace_callback('/__[a-zA-Z0-9]+__/', 
                        function  ($input) use( $correctAnswersArr)
                        {
                            static $count = 0;
                            if (! is_array($correctAnswersArr)) {
                                throw new \exception(
                                        "correct answers isnt an array.");
                            }
                            if (array_key_exists($count, $correctAnswersArr)) {
                                $output = "__" . $correctAnswersArr[$count] .
                                         "__";
                            } else {
                                $output = "__blank__";
                            }
                            $count ++;
                            return $output;
                        }, $questionObj->getTitle());
                
                $data = array(
                        'question' => $questionString
                );
                $form->populate($data);
                
                if ($this->_request->isPost()) {
                    $receivedData = $this->_request->getParams();
                    
                    if ($form->isValid($receivedData)) {
                        $response = $this->_service->acl_updateFillInTheBlankQuestion(
                                $questionObj->getId(), 
                                $form->getValue('question'));
                        if ($response) {
                            $this->_redirect(
                                    "/assessment/edit/" .
                                             $questionObj->getAssessment()
                                                ->getId());
                        }
                    }
                }
                
                break;
            default:
                
                throw new \exception(
                        "An incorrect question type was provided: " .
                                 $questionObj->getClass());
        }
        $this->view->assessment = $questionObj->getAssessment();
        
        $this->view->form = $form;
    }

    public function deleteQuestionAction ()
    {
        $questionObj = $this->_service->acl_findQuestion(
                $this->_request->getParam('id'));
        $assessmentObj = $questionObj->getAssessment()->getCourse();
        $this->view->assessment = $assessmentObj;
        $form = new \App\Form\Course\Assessment\Question\Delete();
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {
                $response = $this->_service->acl_removeQuestion(
                        $questionObj->getId());
                if ($response) {
                    $this->_redirect(
                            "/assessment/edit/" . $assessmentObj->getId());
                }
            }
        }
        $this->view->form = $form;
    }
    
    // Take the assessment -- or list any/all currently open assessments + allow
    // resuming
    public function takeAction ()
    {
        $attemptId = $this->_service->acl_takeAssessment(
                $this->_request->getParam('id'));
        $this->_redirect('/assessment/view-question/' . $attemptId . "/0");
    }

    public function viewquestionAction ()
    {
        $attemptId = $this->_request->getParam('attemptId');
        // Local question order for the attempt -- does not correlate to global
        // question ID.
        $questionNumber = $this->_request->getParam('questionNumber');
        $attemptObj = $this->_service->acl_findAttempt($attemptId);
        if (! is_object($attemptObj)) {
            throw new \exception("Unable to locate that attempt.");
        }
        $assessmentObj = $attemptObj->getAssessment();
        if ($this->_service->acl_getIsFinished($attemptId)) {
            $this->_redirect("/assessment/view/" . $assessmentObj->getId());
        }         // Attempt is not
          // complete --
          // assumed to be
          // the typical
          // case.
        else {
            
            $questionList = $this->_service->acl_fetchQuestions($attemptId);
            $submittedAnswers = $this->_service->acl_fetchSubmittedAnswers(
                    $attemptId);
            if (! array_key_exists($questionNumber, $questionList)) {
                throw new \exception(
                        "Unable to locate that question in the list of questions.");
            }
            $questionObjAsArr = $questionList[$questionNumber];
            
            switch ($questionObjAsArr['class']) {
                case 'App\Entity\Course\Assessment\Question\MultipleChoice':
                    $form = new \App\Form\Course\Assessment\Question\MultipleChoice();
                    break;
                
                case 'App\Entity\Course\Assessment\Question\TrueFalse':
                    $form = new \App\Form\Course\Assessment\Question\TrueFalse();
                    break;
                
                case 'App\Entity\Course\Assessment\Question\FillInTheBlank':
                    $form = new \App\Form\Course\Assessment\Question\FillInTheBlank();
                    break;
            }
            
            $this->view->form = $form;
            $this->view->attemptId = $attemptId;
            $this->view->question = $questionObjAsArr;
            $this->view->questionNumber = $questionNumber;
            $this->view->questionList = $questionList;
            $this->view->assessment = $assessmentObj;
            $this->view->submittedAnswers = $this->_service->acl_fetchSubmittedAnswers(
                    $attemptId);
        }
    }
    
    // view results of an individual attempt (shown after student 'finishes'
    // attempt.
    public function viewresultsAction ()
    {
        $attemptId = $this->_request->getParam('attemptId');
        $attemptObj = $this->_service->acl_findAttempt($attemptId);
        if (! is_object($attemptObj)) {
            throw new \exception("Unable to locate that attempt.");
        }
        $assessmentObj = $attemptObj->getAssessment();
        if (! $this->_service->acl_getIsFinished($attemptId)) {
            $this->_redirect("/assessment/view/" . $assessmentObj->getId());
        } else {
            
            $questionList = $this->_service->acl_fetchQuestions($attemptId);
            
            $this->view->attemptId = $attemptId;
            $this->view->attempt = $attemptObj;
            $this->view->questionList = $questionList;
            $this->view->assessment = $assessmentObj;
            $this->view->submittedAnswers = $this->_service->acl_fetchSubmittedAnswers(
                    $attemptId);
        }
    }

    public function viewstudentscoresAction ()
    {
        // 1. get all attempts
        $assessmentObj = $this->_service->acl_find(
                $this->_request->getParam('id'));
        if (! is_object($assessmentObj)) {
            throw new \exception("Unable to locate that assessment.");
        }
        // 2. display scores and so on.
        $this->view->assessment = $assessmentObj;
        
        // 3. allow link to view results page to see individual score.
    }
}

