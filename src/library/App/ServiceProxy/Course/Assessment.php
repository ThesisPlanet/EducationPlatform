<?php
namespace App\ServiceProxy\Course;

class Assessment extends \App\ServiceProxy\aService implements 
        \App\Service\Course\iAssessment
{

    protected function loadService ()
    {
        $this->_service = new \App\Service\Course\Assessment();
    }
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_create()
     */
    public function acl_create ($courseId, $title, $description)
    {
        return $this->_service->acl_create($courseId, $title, $description);
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_delete()
     */
    public function acl_delete ($assessmentId)
    {
        return $this->_service->acl_delete($assessmentId);
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Course\iAssessment::acl_addMultipleChoiceQuestion()
     */
    public function acl_addMultipleChoiceQuestion ($assessmentId, $questionText, 
            $possibleAnswersArr, $correctArr)
    {
        return $this->_service->acl_addMultipleChoiceQuestion($assessmentId, 
                $questionText, $possibleAnswersArr, $correctArr);
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Course\iAssessment::acl_addTrueFalseQuestion()
     */
    public function acl_addTrueFalseQuestion ($assessmentId, $questionString, 
            $answerBoolean)
    {
        return $this->_service->acl_addTrueFalseQuestion($assessmentId, 
                $questionString, $answerBoolean);
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Course\iAssessment::acl_addFillInTheBlankQuestion()
     */
    public function acl_addFillInTheBlankQuestion ($assessmentId, 
            $questionString)
    {
        return $this->_service->acl_addFillInTheBlankQuestion($assessmentId, 
                $questionString);
    }

    public function acl_find ($assessmentId)
    {
        return $this->_service->acl_find($assessmentId);
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_findByCourse()
     */
    public function acl_findByCourse ($courseId)
    {
        return $this->_service->acl_findByCourse($courseId);
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_removeQuestion()
     */
    public function acl_removeQuestion ($questionId)
    {
        return $this->_service->acl_removeQuestion($questionId);
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_answerQuestion()
     */
    public function acl_answerQuestion ($attemptId, $questionId, $answer)
    {
        return $this->_service->acl_answerQuestion($attemptId, $questionId, 
                $answer);
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_takeAssessment()
     */
    public function acl_takeAssessment ($assessmentId)
    {
        return $this->_service->acl_takeAssessment($assessmentId);
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Course\iAssessment::acl_setIsRetakeAllowed()
     */
    public function acl_setIsRetakeAllowed ($assessmentId, $allowRetakesBoolean)
    {
        return $this->_service->acl_setIsRetakeAllowed($assessmentId, 
                $allowRetakesBoolean);
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_fetchQuestions()
     */
    public function acl_fetchQuestions ($attemptId)
    {
        return $this->_service->acl_fetchQuestions($attemptId);
    }
    
    
    public function acl_fetchSubmittedAnswers($attemptId)
    {
        return $this->_service->acl_fetchSubmittedAnswers($attemptId);
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_finishAttempt()
     */
    public function acl_finishAttempt ($attemptId)
    {
        return $this->_service->acl_finishAttempt($attemptId);
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Course\iAssessment::acl_fetchAttemptResults()
     */
    public function acl_fetchAttemptResults ($attemptId)
    {
        return $this->_service->acl_fetchAttemptResults($attemptId);
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_findAllScores()
     */
    public function acl_findAllScores ($assessmentId)
    {
        return $this->_service->acl_findAllScores($assessmentId);
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Course\iAssessment::acl_findResultsByQuestion()
     */
    public function acl_findResultsByQuestion ($questionId)
    {
        return $this->_service->acl_findResultsByQuestion($questionId);
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_getIsFinished()
     */
    public function acl_getIsFinished ($attemptId)
    {
        return $this->_service->acl_getIsFinished($attemptId);
    }
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_findQuestion()
     */
    public function acl_findQuestion ($questionId)
    {
        return $this->_service->acl_findQuestion($questionId);
    }
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_listAttempts()
     */
    public function acl_listAttempts ($assessmentId)
    {
        $attemptList = $this->_service->acl_listAttempts($assessmentId);
        $out = array();
        foreach ($attemptList as $attemptObj) {
            $addMe = $attemptObj->toArray();
            unset($addme['answers']);
            $out[] = $addMe;
        }
        return $out;
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\Course\iAssessment::acl_findAttempt()
     */
    public function acl_findAttempt ($attemptId)
    {
        return $this->_service->acl_findAttempt($attemptId);
    }
}