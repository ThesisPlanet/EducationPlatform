<?php
namespace App\Service;

class Question extends Base implements iQuestion
{

    const QUESTION_NOT_FOUND = "Unable to find that question";

    const CONTENT_NOT_FOUND = "Unable to find that piece of content";

    public function __construct ()
    {
        $this->_em = \Zend_Registry::get('em');
        $this->_acl = new \App\Service\ACL\Question();
        $this->_courseRepository = $this->_em->getRepository(
                '\App\Entity\Course');
        $this->_courseQuestionRepository = $this->_em->getRepository(
                '\App\Entity\Course\Question');
        $this->_courseQuestionAnswerRepository = $this->_em->getRepository(
                '\App\Entity\Course\Question\Answer');
        
        $this->_contentQuestionRepository = $this->_em->getRepository(
                '\App\Entity\Content\Question');
        $this->_contentQuestionAnswerRepository = $this->_em->getRepository(
                '\App\Entity\Content\Question\Answer');
        
        $this->_userRepository = $this->_em->getRepository('\App\Entity\User');
        
        $this->_audioRepository = $this->_em->getRepository(
                '\App\Entity\Content\Audio');
        
        $this->_fileRepository = $this->_em->getRepository(
                '\App\Entity\Content\File');
        
        $this->_videoRepository = $this->_em->getRepository(
                '\App\Entity\Content\Video');
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
     * (non-PHPdoc) @see \App\Service\iQuestion::acl_askCourseQuestion()
     */
    public function acl_askCourseQuestion ($courseId, $questionText)
    {
        $courseObj = $this->_courseRepository->find($courseId);
        if (is_object($courseObj)) {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        
        $form = new \App\Form\Course\Question();
        if (! $form->isValid(
                array(
                        'questionText' => $questionText
                ))) {
            throw new \exception(self::FORM_INVALID);
        }
        $questionObj = new \App\Entity\Course\Question();
        $questionObj->setText($form->getValue('questionText'));
        $questionObj->setCourse($courseObj);
        $questionObj->setUser($this->_user);
        $questionObj->setCreatedAt(new \DateTime());
        $this->_em->persist($questionObj);
        $this->_em->flush();
        
        try {
            $cl = \Zend_Registry::getInstance()->get('queue');
            $jobParams = array(
                    'environment' => APPLICATION_ENV,
                    'templateName' => "CourseUserAsksQuestion.phtml",
                    'emailSubject' => "New question posted for: " .
                             $courseObj->getTitle(),
                            'courseId' => $courseObj->getId(),
                            'templateParameters' => array(
                                    'course' => array(
                                            'id' => $courseObj->getId(),
                                            'title' => $courseObj->getTitle()
                                    ),
                                    'questioner' => array(
                                            'id' => $this->_user->getId(),
                                            'firstname' => $this->_user->getFirstname(),
                                            'lastname' => $this->_user->getLastname()
                                    ),
                                    'question' => array(
                                            'id' => $questionObj->getId(),
                                            'text' => $questionObj->getText()
                                    )
                            )
            );
            $cl->backgroundTask('DEP_Notification_SendCourseWideNotification', 
                    $jobParams);
        } catch (\exception $e) {
            throw new \exception(
                    "There was a problem creating the processing task to handle the
              post-upload activities." . $e->getMessage());
        }
        
        return $questionObj->getId();
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\iQuestion::acl_answerCourseQuestion()
     */
    public function acl_answerCourseQuestion ($questionId, $answerText)
    {
        $questionObj = $this->_courseQuestionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        }
        $courseObj = $questionObj->getCourse();
        if (is_object($courseObj)) {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        
        $form = new \App\Form\Course\Question\Answer();
        if (! $form->isValid(
                array(
                        'questionId' => $questionObj->getId(),
                        'answerText' => $answerText
                ))) {
            throw new \exception(self::FORM_INVALID);
        }
        $answerObj = new \App\Entity\Course\Question\Answer();
        $answerObj->setQuestion($questionObj);
        $answerObj->setCreatedAt(new \DateTime());
        $answerObj->setText($form->getValue('answerText'));
        $answerObj->setUser($this->_user);
        $this->_em->persist($answerObj);
        $this->_em->flush();
        
        try {
            $cl = \Zend_Registry::getInstance()->get('queue');
            $jobParams = array(
                    'environment' => APPLICATION_ENV,
                    'templateName' => "CourseUserAnswersQuestion.phtml",
                    'emailSubject' => "A question has been answered in the following course: " .
                             $courseObj->getTitle(),
                            'courseId' => $courseObj->getId(),
                            'templateParameters' => array(
                                    'course' => array(
                                            'id' => $courseObj->getId(),
                                            'title' => $courseObj->getTitle()
                                    ),
                                    'answerer' => array(
                                            'id' => $this->_user->getId(),
                                            'firstname' => $this->_user->getFirstname(),
                                            'lastname' => $this->_user->getLastname()
                                    ),
                                    'question' => array(
                                            'id' => $questionObj->getId(),
                                            'text' => $questionObj->getText()
                                    ),
                                    'answer' => array(
                                            'id' => $answerObj->getId(),
                                            'text' => $answerObj->getText()
                                    )
                            )
            );
            
            $cl->backgroundTask('DEP_Notification_SendCourseWideNotification', 
                    $jobParams);
        } catch (\exception $e) {
            throw new \exception(
                    "There was a problem creating the processing task to handle the
              post-upload activities." . $e->getMessage());
        }
        
        return $answerObj->getId();
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\iQuestion::acl_removeCourseQuestion()
     */
    public function acl_removeCourseQuestion ($questionId)
    {
        $questionObj = $this->_courseQuestionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        }
        $courseObj = $questionObj->getCourse();
        if (is_object($courseObj)) {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        $this->_em->remove($questionObj);
        $this->_em->flush();
        return true;
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\iQuestion::acl_listCourseQuestions()
     */
    public function acl_listCourseQuestions ($courseId)
    {
        $courseObj = $this->_courseRepository->find($courseId);
        if (is_object($courseObj)) {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        
        $questions = $this->_courseQuestionRepository->findBy(
                array(
                        '_course' => $courseObj
                ), 
                array(
                        'createdAt' => 'DESC'
                ));
        return $questions;
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\iQuestion::acl_askContentQuestion()
     */
    public function acl_askContentQuestion ($contentId, $contentType, 
            $questionText)
    {
        $repo = null;
        switch (strtolower($contentType)) {
            case "audio":
                $repo = $this->_audioRepository;
                break;
            
            case "file":
                $repo = $this->_fileRepository;
            case "video":
                $repo = $this->_videoRepository;
                break;
            default:
                throw new \exception("Please provide a valid content type");
        }
        
        $contentObj = $repo->find($contentId);
        if (! is_object($contentObj)) {
            throw new \exception(self::CONTENT_NOT_FOUND);
        }
        $courseObj = $contentObj->getCourse();
        
        if (is_object($courseObj)) {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        
        $form = new \App\Form\Content\Question();
        if (! $form->isValid(
                array(
                        'questionText' => $questionText
                ))) {
            throw new \exception(self::FORM_INVALID);
        }
        $questionObj = new \App\Entity\Content\Question();
        $questionObj->setText($form->getValue('questionText'));
        $questionObj->setContent($contentObj);
        $questionObj->setUser($this->_user);
        $questionObj->setCreatedAt(new \DateTime());
        $this->_em->persist($questionObj);
        $this->_em->flush();
        return $questionObj->getId();
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\iQuestion::acl_answerContentQuestion()
     */
    public function acl_answerContentQuestion ($questionId, $answerText)
    {
        $questionObj = $this->_contentQuestionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        }
        $contentObj = $questionObj->getContent();
        if (is_object($contentObj)) {
            if (! $this->isAllowed($contentObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        
        $form = new \App\Form\Content\Question\Answer();
        if (! $form->isValid(
                array(
                        'questionId' => $questionObj->getId(),
                        'answerText' => $answerText
                ))) {
            throw new \exception(self::FORM_INVALID);
        }
        $answerObj = new \App\Entity\Content\Question\Answer();
        $answerObj->setQuestion($questionObj);
        $answerObj->setCreatedAt(new \DateTime());
        $answerObj->setText($form->getValue('answerText'));
        $answerObj->setUser($this->_user);
        $this->_em->persist($answerObj);
        $this->_em->flush();
        return $answerObj->getId();
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\iQuestion::acl_removeContentQuestion()
     */
    public function acl_removeContentQuestion ($questionId)
    {
        $questionObj = $this->_contentQuestionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::CONTENT_NOT_FOUND);
        }
        $courseObj = $questionObj->getContent()->getCourse();
        
        if (is_object($courseObj)) {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        $this->_em->remove($questionObj);
        $this->_em->flush();
        return true;
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\iQuestion::acl_listContentQuestions()
     */
    public function acl_listContentQuestions ($contentId, $contentType)
    {
        $repo = null;
        switch (strtolower($contentType)) {
            case "audio":
                $repo = $this->_audioRepository;
                break;
            
            case "file":
                $repo = $this->_fileRepository;
            case "video":
                $repo = $this->_videoRepository;
                break;
            default:
                throw new \exception("Please provide a valid content type");
        }
        
        $contentObj = $repo->find($contentId);
        if (! is_object($contentObj)) {
            throw new \exception(self::CONTENT_NOT_FOUND);
        }
        $courseObj = $contentObj->getCourse();
        
        if (is_object($courseObj)) {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        
        $questions = $this->_contentQuestionRepository->findBy(
                array(
                        '_content' => $contentObj
                ), 
                array(
                        'createdAt' => 'DESC'
                ));
        return $questions;
    }
    /*
     * (non-PHPdoc) @see \App\Service\iQuestion::acl_listCourseQuestionAnswers()
     */
    public function acl_listCourseQuestionAnswers ($questionId)
    {
        $questionObj = $this->_courseQuestionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        }
        $courseObj = $questionObj->getCourse();
        
        if (is_object($courseObj)) {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        
        $questions = $this->_courseQuestionAnswerRepository->findBy(
                array(
                        '_question' => $questionObj
                ), 
                array(
                        'createdAt' => 'DESC'
                ));
        return $questions;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \App\Service\iQuestion::acl_removeCourseQuestionAnswer()
     */
    public function acl_removeCourseQuestionAnswer ($answerId)
    {
        $questionAnswerObj = $this->_courseQuestionAnswerRepository->find(
                $answerId);
        if (! is_object($questionAnswerObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        }
        
        $courseObj = $questionAnswerObj->getQuestion()->getCourse();
        if (is_object($courseObj)) {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        $this->_em->remove($questionAnswerObj);
        $this->_em->flush();
        return true;
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\iQuestion::acl_listContentQuestionAnswers()
     */
    public function acl_listContentQuestionAnswers ($questionId)
    {
        $questionObj = $this->_contentQuestionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        }
        $contentObj = $questionObj->getContent();
        
        if (is_object($contentObj)) {
            if (! $this->isAllowed($contentObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        
        $questions = $this->_contentQuestionAnswerRepository->findBy(
                array(
                        '_question' => $questionObj
                ), 
                array(
                        'createdAt' => 'DESC'
                ));
        return $questions;
    }
    /*
     * (non-PHPdoc) @see \App\Service\iQuestion::acl_findCourseQuestion()
     */
    public function acl_findCourseQuestion ($questionId)
    {
        $questionObj = $this->_courseQuestionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        }
        $courseObj = $questionObj->getCourse();
        if (is_object($courseObj)) {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        
        return $questionObj;
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\iQuestion::acl_findContentQuestion()
     */
    public function acl_findContentQuestion ($questionId)
    {
        $questionObj = $this->_contentQuestionRepository->find($questionId);
        if (! is_object($questionObj)) {
            throw new \exception(self::QUESTION_NOT_FOUND);
        }
        $courseObj = $questionObj->getCourse();
        if (is_object($courseObj)) {
            if (! $this->isAllowed($courseObj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::COURSE_NOT_FOUND);
        }
        
        return $questionObj;
    }
}