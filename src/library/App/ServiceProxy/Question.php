<?php
namespace App\ServiceProxy;

class Question extends aService implements \App\Service\iQuestion
{

    protected function loadService ()
    {
        $this->_service = new \App\Service\Question();
    }

    /**
     *
     * @param integer $courseId
     * @param string $questionText
     * @return integer
     */
    public function acl_askCourseQuestion ($courseId, $questionText)
    {
        return $this->_service->acl_askCourseQuestion($courseId, $questionText);
    }

    /**
     *
     * @param integer $questionId
     * @param string $answerText
     * @return integer
     * @see \App\Service\iQuestion::acl_answerCourseQuestion()
     */
    public function acl_answerCourseQuestion ($questionId, $answerText)
    {
        return $this->_service->acl_answerCourseQuestion($questionId,
                $answerText);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \App\Service\iQuestion::acl_removeCourseQuestion()
     * @param integer $questionId
     * @return boolean
     */
    public function acl_removeCourseQuestion ($questionId)
    {
        return $this->_service->acl_removeCourseQuestion($questionId);
    }

    /**
     *
     * @param integer $courseId
     * @return array
     * @see \App\Service\iQuestion::acl_listCourseQuestions()
     */
    public function acl_listCourseQuestions ($courseId)
    {
        $result = $this->_service->acl_listCourseQuestions($courseId);

        $outArry = array();
        if (is_array($result)) {
            foreach ($result as $key => $obj) {
                $outArry[$key] = $obj->toArray();
            }
        }
        return $outArry;
    }

    /**
     *
     * @param integer $contentId
     * @param string $contentType
     * @param string $questionText
     * @return integer
     * @see \App\Service\iQuestion::acl_askContentQuestion()
     */
    public function acl_askContentQuestion ($contentId, $contentType,
            $questionText)
    {
        return $this->_service->acl_askContentQuestion($contentId, $contentType,
                $questionText);
    }

    /**
     *
     * @param integer $questionId
     * @param string $answerText
     * @return integer
     * @see \App\Service\iQuestion::acl_answerContentQuestion()
     */
    public function acl_answerContentQuestion ($questionId, $answerText)
    {
        return $this->_service->acl_answerContentQuestion($questionId,
                $answerText);
    }

    /**
     *
     * @param integer $questionId
     * @return boolean
     * @see \App\Service\iQuestion::acl_removeContentQuestion()
     */
    public function acl_removeContentQuestion ($questionId)
    {
        return $this->_service->acl_removeContentQuestion($questionId);
    }

    /**
     *
     * @see \App\Service\iQuestion::acl_removeCourseQuestionAnswer()
     * @param integer $answerId
     * @return boolean
     */
    public function acl_removeCourseQuestionAnswer ($answerId)
    {
        return $this->_service->acl_removeCourseQuestionAnswer($answerId);
    }

    /**
     *
     * @param integer $contentId
     * @param string $contentType
     * @return array
     * @see \App\Service\iQuestion::acl_listContentQuestions()
     */
    public function acl_listContentQuestions ($contentId, $contentType)
    {
        $result = $this->_service->acl_listContentQuestions($contentId,
                $contentType);

        $outArry = array();
        if (is_array($result)) {
            foreach ($result as $key => $obj) {
                $outArry[$key] = $obj->toArray();
            }
        }
        return $outArry;
    }

    /**
     *
     * @param integer $questionId
     * @return array
     * @see \App\Service\iQuestion::acl_listCourseQuestionAnswers()
     */
    public function acl_listCourseQuestionAnswers ($questionId)
    {
        $result = $this->_service->acl_listCourseQuestionAnswers($questionId);

        $outArry = array();
        if (is_array($result)) {
            foreach ($result as $key => $obj) {
                $outArry[$key] = $obj->toArray();
            }
        }
        return $outArry;
    }

    /**
     *
     * @param integer $questionId
     * @return array
     * @see \App\Service\iQuestion::acl_listContentQuestionAnswers()
     */
    public function acl_listContentQuestionAnswers ($questionId)
    {
        $result = $this->_service->acl_listContentQuestionAnswers($questionId);
        $outArry = array();
        if (is_array($result)) {
            foreach ($result as $key => $obj) {
                $outArry[$key] = $obj->toArray();
            }
        }
        return $outArry;
    }

    /**
     *
     * @param integer $questionId
     * @return array
     * @see \App\Service\iQuestion::acl_findCourseQuestion()
     */
    public function acl_findCourseQuestion ($questionId)
    {
        $result = $this->_service->acl_findCourseQuestion($questionId);

        return $result->toArray();
    }

    /**
     *
     * @param integer $questionId
     * @return array
     * @see \App\Service\iQuestion::acl_findContentQuestion()
     */
    public function acl_findContentQuestion ($questionId)
    {
        $result = $this->_service->acl_findContentQuestion($questionId);

        return $result->toArray();
    }
}