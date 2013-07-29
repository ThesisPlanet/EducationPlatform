<?php
namespace App\Service\Course;

interface iAssessment
{

    /**
     * Create a new assessment for a given course.
     * Returns the assessment ID
     *
     * @param integer $courseId            
     * @param string $title            
     * @param string $description            
     * @return integer $assessmentId
     * @throws \Exception
     */
    public function acl_create ($courseId, $title, $description);

    /**
     * Delete an existing assessment
     *
     * @param integer $assessmentId            
     * @return boolean
     * @throws \Exception
     */
    public function acl_delete ($assessmentId);

    /**
     * Add a new multiple choice question to an assessment
     *
     * @param integer $assessmentId            
     * @param string $questionText            
     * @param array $possibleAnswersArr            
     * @param array $correctArr            
     * @return integer $questionId
     * @throws \Exception
     */
    public function acl_addMultipleChoiceQuestion ($assessmentId, $questionText, 
            $possibleAnswersArr, $correctArr);

    /**
     * Add a new True/False question to an assessment
     *
     * @param integer $assessmentId            
     * @param string $questionString            
     * @param boolean $answerBoolean            
     * @return integer $questionId
     * @throws \Exception
     */
    public function acl_addTrueFalseQuestion ($assessmentId, $questionString, 
            $answerBoolean);

    /**
     * Add a new Fill In The Blank question
     *
     * @param integer $assessmentId            
     * @param string $questionString            
     * @return integer $questionId
     * @throws \Exception
     */
    public function acl_addFillInTheBlankQuestion ($assessmentId, 
            $questionString);

    public function acl_find ($assessmentId);

    /**
     * Find a question object
     *
     * @param integer $questionId            
     * @return \App\Entity\Course\Assessment\Question
     * @throws \Exception
     */
    public function acl_findQuestion ($questionId);

    /**
     * List all assessments by the course
     *
     * @param integer $courseId            
     * @return array
     */
    public function acl_findByCourse ($courseId);

    /**
     * Delete a question from an Assessment
     *
     * @param integer $questionId            
     * @return boolean
     * @throws \Exception
     */
    public function acl_removeQuestion ($questionId);

    /**
     * Answer a question
     *
     * @param integer $attemptId            
     * @param integer $questionId            
     * @param mixed $answer            
     */
    public function acl_answerQuestion ($attemptId, $questionId, $answer);

    /**
     * Take or Retake an assessment if allowed
     *
     * @param integer $assessmentId            
     * @return integer $attemptId
     * @throws \Exception
     */
    public function acl_takeAssessment ($assessmentId);

    /**
     * Set whether or not individual can make multiple attempts on an
     * assessment.
     * Default: True
     *
     * @param integer $assessmentId            
     * @param boolean $allowRetakesBoolean            
     * @return boolean
     * @throws \Exception
     */
    public function acl_setIsRetakeAllowed ($assessmentId, $allowRetakesBoolean);

    /**
     * Fetch all of the questions from an attempt.
     *
     * @param integer $attemptId            
     * @return array
     * @throws \Exception
     */
    public function acl_fetchQuestions ($attemptId);

    /**
     * Submit and score an attempt.
     * If viewing is allowed, answers can be seen when calling fetchResults.
     *
     * @param integer $attemptId            
     * @return boolean
     * @throws \Exception
     */
    public function acl_finishAttempt ($attemptId);

    /**
     * Get the score and answers (if allowed) of an attempted assessment.
     *
     * @param integer $attemptId            
     * @return array
     * @throws \Exception
     */
    public function acl_fetchAttemptResults ($attemptId);

    /**
     * Provide the scores for all attempts taken by all users
     *
     * @param integer $assessmentId            
     * @return array
     * @throws \Exception
     */
    public function acl_findAllScores ($assessmentId);

    /**
     * Return the scores for all users for a given question
     *
     * @param integer $assessmentId            
     * @return array
     * @throws \Exception
     */
    public function acl_findResultsByQuestion ($questionId);

    /**
     * Specify whether or not the user has finished attempting the assessment
     *
     * @param integer $attemptId            
     * @return boolean
     */
    public function acl_getIsFinished ($attemptId);

    public function acl_listAttempts ($assessmentId);

    public function acl_findAttempt ($attemptId);

    public function acl_fetchSubmittedAnswers ($attemptId);
}