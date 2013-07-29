<?php
namespace App\Service;

interface iQuestion
{

    public function acl_askCourseQuestion ($courseId, $questionText);

    public function acl_answerCourseQuestion ($questionId, $answerText);

    public function acl_removeCourseQuestion ($questionId);

    public function acl_listCourseQuestions ($courseId);

    public function acl_listCourseQuestionAnswers ($questionId);

    public function acl_findCourseQuestion($questionId);

    public function acl_findContentQuestion($questionId);

    public function acl_askContentQuestion ($contentId, $contentType,
            $questionText);

    public function acl_answerContentQuestion ($questionId, $answerText);

    public function acl_removeContentQuestion ($questionId);

    public function acl_removeCourseQuestionAnswer ($answerId);

    public function acl_listContentQuestions ($contentId, $contentType);

    public function acl_listContentQuestionAnswers ($questionId);
}