<?php
namespace App\ServiceProxy;

class Course extends aService implements \App\Service\iCourse
{

    protected function loadService ()
    {
        $this->_service = new \App\Service\Course();
    }

    /**
     *
     * @param integer $id            
     * @return array
     */
    public function find ($id)
    {
        return $this->_service->find($id)->toArray();
    }

    /**
     * Finds all of the courses available.
     *
     * @return array
     */
    public function findAll ()
    {
        $result = $this->_service->findAll();
        $outArry = array();
        if (is_array($result)) {
            foreach ($result as $key => $obj) {
                $outArry[$key] = $obj->toArray();
            }
        }
        return $outArry;
    }

    /**
     * Find courses by title
     *
     * @return array
     */
    public function findOneByTitle ($title)
    {
        $result = $this->_service->findByTitle($title);
        $outArry = array();
        if (is_array($result)) {
            foreach ($result as $key => $obj) {
                $outArry[$key] = $obj->toArray();
            }
        }
        return $outArry;
    }

    /**
     * Search for courses
     *
     * @param string $terms            
     * @return array
     */
    public function search ($terms)
    {
        $result = $this->_service->search($terms);
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
     * @param integer $courseId            
     * @return string html form
     */
    public function getForm ($courseId = null)
    {
        return $this->_service->getForm($courseId)->__toString();
    }

    /**
     *
     * @return string
     * @see \App\Service\iCourse::getAddChapterForm()
     */
    public function getAddChapterForm ()
    {
        return $this->_service->getAddChapterForm();
    }

    /**
     * Adds a new chapter to a course.
     *
     * @param integer $courseId            
     * @param string $text            
     * @return integer
     */
    public function acl_addChapter ($courseId, $text)
    {
        return $this->_service->acl_addChapter($courseId, $text);
    }

    /**
     * Update the order of chapters inside of a course.
     *
     * @param integer $courseId            
     * @param array $chapterIDsArray            
     * @return boolean
     */
    public function acl_updateChapterOrder ($courseId, $chapterIDsArray)
    {
        return $this->_service->acl_updateChapterOrder($courseId, 
                $chapterIDsArray);
    }

    /**
     *
     * @see \App\Service\iCourse::acl_listChapters()
     * @param integer $courseId            
     * @return array
     */
    public function acl_listChapters ($courseId)
    {
        $result = $this->_service->acl_listChapters($courseId);
        $outArry = array();
        if (is_array($result)) {
            foreach ($result as $key => $obj) {
                $outArry[$key] = $obj->toArray();
            }
        }
        return $outArry;
    }

    /**
     * Find courses within a given topic
     *
     * @param string $topic            
     * @return array
     */
    public function findByTopic ($topic)
    {
        $result = $this->_service->findByTopic($topic);
        $outArry = array();
        if (is_array($result)) {
            foreach ($result as $key => $obj) {
                $outArry[$key] = $obj->toArray();
            }
        }
        return $outArry;
    }

    /**
     * return a lsit of subscriptions
     *
     * @see \App\Service\iCourse::acl_findMySubscriptions()
     * @return array
     */
    public function acl_findSubscriptions ()
    {
        $result = $this->_service->acl_findSubscriptions();
        
        $outArry = array();
        if (is_array($result)) {
            foreach ($result as $key => $obj) {
                $outArry[$key] = $obj->toArray();
            }
        }
        return $outArry;
    }

    public function acl_findSubscription ($courseId)
    {
        $result = $this->_service->acl_findSubscription($courseId);
        if (is_object($result)) {
            return $result->toArray();
        } else {
            return false;
        }
    }

    /**
     * Users should be able to create a new course
     *
     * @param array $data            
     * @return integer
     */
    public function acl_create (array $data)
    {
        return $this->_service->acl_create($data);
    }

    /**
     * Providers should be able to update the topic, description, pricing, etc
     * related to their course.
     *
     * @param integer $id            
     * @param array $data            
     * @return boolean
     */
    public function acl_update ($id, array $data)
    {
        return $this->_service->acl_update($id, $data);
    }

    /**
     * Administrators only can delete a course.
     *
     * @param integer $id            
     * @return boolean
     */
    public function acl_delete ($id)
    {
        return $this->_service->acl_delete($id);
    }

    /**
     * Course Owners should be able to publish a course once it is ready.
     *
     * @param integer $courseId            
     * @return boolean
     */
    public function acl_publish ($courseId)
    {
        return $this->_service->acl_publish($courseId);
    }

    /**
     * Course Owners should be able to unpublish a course in case they
     * accidentally published it.
     *
     * @param integer $courseId            
     * @return boolean
     */
    public function acl_unpublish ($courseId)
    {
        return $this->_service->acl_unpublish($courseId);
    }

    /**
     * Administrators (Employees) should be able to disable a course if there is
     * a problem with it.
     *
     * @param integer $courseId            
     * @return boolean
     */
    public function acl_disable ($courseId)
    {
        return $this->_service->acl_disable($courseId);
    }

    /**
     * Administrators (Employees) should be able to enable a course once the
     * problem has been resolved.
     *
     * @param integer $courseId            
     * @return boolean
     */
    public function acl_enable ($courseId)
    {
        return $this->_service->acl_enable($courseId);
    }

    /**
     * Subscribers should be able to add a review to a course.
     *
     * @param integer $courseId            
     * @param boolean $isRecommended            
     * @param string $reviewText            
     * @return integer
     */
    public function acl_addReview ($courseId, $isRecommended, $reviewText)
    {
        return $this->_service->acl_addReview($courseId, $isRecommended, 
                $reviewText);
    }

    /**
     * Users should be able to subscribe to a course.
     *
     * @param integer $courseId            
     * @return boolean
     */
    public function acl_subscribe ($courseId)
    {
        return $this->_service->acl_subscribe($courseId);
    }

    /**
     * Course providers should be able to approve subscription requests.
     * Applies to courses that have approval requirements
     *
     * @param integer $courseId            
     * @param integer $userId            
     * @return boolean
     */
    public function acl_approveSubscribeRequest ($courseId, $userId)
    {
        return $this->_service->acl_approveSubscribeRequest($courseId, $userId);
    }

    /**
     * Course providers should be able to reject subscription requests.
     * Applies to courses that have approval requirements
     *
     * @param integer $courseId            
     * @param integer $userId            
     * @return boolean
     */
    public function acl_denySubscribeRequest ($courseId, $userId)
    {
        return $this->_service->acl_denySubscribeRequest($courseId, $userId);
    }

    /**
     * Subscribers should be able to unsubscribe from a course.
     *
     * @param integer $courseId            
     * @return boolean
     */
    public function acl_unsubscribe ($courseId)
    {
        return $this->_service->acl_unsubscribe($courseId);
    }

    /**
     * Users should be able to accept an invitation from a course provider
     *
     * @param integer $courseId            
     * @param integer $userId            
     * @return boolean
     */
    public function acl_userAcceptInvite ($courseId, $userId)
    {
        return $this->_service->acl_userAcceptInvite($courseId, $userId);
    }

    /**
     * Users should be able to reject an invitation from a course provider
     *
     * @param integer $courseId            
     * @param integer $userId            
     * @return boolean
     */
    public function acl_userRejectInvite ($courseId, $userId)
    {
        return $this->_service->acl_userRejectInvite($courseId, $userId);
    }

    /**
     * Subscribers should be able to remove their review that they wrote about a
     * course.
     *
     * @param integer $reviewId            
     * @return boolean
     */
    public function acl_removeReview ($reviewId)
    {
        return $this->_service->acl_removeReview($reviewId);
    }

    /**
     * Update the main image for the course
     *
     * @param integer $courseId            
     * @return boolean
     */
    public function acl_updateImage ($courseId)
    {
        return $this->_service->acl_updateImage($courseId);
    }

    /**
     * Delete a chapter
     *
     * @param integer $chapterId            
     * @return boolean
     */
    public function acl_deleteChapter ($chapterId)
    {
        return $this->_service->acl_deleteChapter($chapterId);
    }

    /**
     *
     * @param integer $courseId            
     * @param integer $contentId            
     * @return boolean
     * @see \App\Service\iCourse::acl_completeContent()
     */
    public function acl_completeContent ($courseId, $contentId)
    {
        return $this->_service->acl_completeContent($courseId, $contentId);
    }

    /**
     *
     * @param integer $courseId            
     * @param integer $contentId            
     * @return boolean
     * @see \App\Service\iCourse::acl_uncompleteContent()
     */
    public function acl_uncompleteContent ($courseId, $contentId)
    {
        return $this->_service->acl_uncompleteContent($courseId, $contentId);
    }

    /**
     *
     * @param integer $courseId            
     * @return float
     */
    public function acl_getPercentComplete ($courseId)
    {
        return $this->_service->acl_getPercentComplete($courseId);
    }

    /**
     *
     * @see \App\Service\iCourse::acl_getCompletedContentList()
     * @param integer $courseId            
     * @return array
     */
    public function acl_getCompletedContentList ($courseId)
    {
        return $this->_service->acl_getCompletedContentList($courseId);
    }

    /**
     * (non-PHPdoc) @see \App\Service\iCourse::acl_providerSubscribeUser()
     *
     * @param integer $courseId            
     * @param string $email            
     * @return integer
     */
    public function acl_providerSubscribeUser ($courseId, $email)
    {
        return $this->_service->acl_providerSubscribeUser($courseId, $email);
    }

    /**
     *
     * @see \App\Service\iCourse::acl_providerDeleteSubscription()
     * @param integer $subscriptionId            
     * @return boolean
     */
    public function acl_providerDeleteSubscription ($subscriptionId)
    {
        return $this->_service->acl_providerRemoveSubscription($subscriptionId);
    }

    /**
     *
     * @see \App\Service\iCourse::acl_providerUpdateSubscriptionRole()
     * @param integer $subscriptionId            
     * @param string $newRole            
     * @return boolean
     */
    public function acl_providerUpdateSubscriptionRole ($subscriptionId, 
            $newRole)
    {
        return $this->_service->acl_providerUpdateSubscriptionRole(
                $subscriptionId, $newRole);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \App\Service\iCourse::acl_providerCreateAnnouncement()
     * @param integer $courseId            
     * @param string $announcementText            
     * @param boolean $shouldNotifyUsers            
     */
    public function acl_providerCreateAnnouncement ($courseId, $announcementText, 
            $shouldNotifyUsers)
    {
        return $this->_service->acl_providerCreateAnnouncement($courseId, 
                $announcementText, $shouldNotifyUsers);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \App\Service\iCourse::acl_providerRemoveAnnouncement()
     * @param integer $announcementId            
     * @return boolean
     * @throws exception
     */
    public function acl_providerRemoveAnnouncement ($announcementId)
    {
        return $this->_service->acl_providerRemoveAnnouncement($announcementId);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \App\Service\iCourse::acl_listAnnouncements()
     * @param integer $courseId            
     * @return array
     */
    public function acl_listAnnouncements ($courseId)
    {
        $result = $this->_service->acl_listAnnouncements($courseId);
        $outArry = array();
        if (is_array($result)) {
            foreach ($result as $key => $obj) {
                $outArry[$key] = $obj->toArray();
            }
        }
        return $outArry;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \App\Service\iCourse::acl_findAnnouncement()
     * @param integer $announcementId            
     * @return array
     */
    public function acl_findAnnouncement ($announcementId)
    {
        $result = $this->_service->acl_findAnnouncement($announcementId);
        if (is_object($result)) {
            return $result->toArray();
        }
    }

    /**
     *
     * @param integer $courseId            
     * @throws \exception
     * @return array
     */
    public function acl_listContentOrder ($courseId)
    {
        $result = $this->_service->acl_listContentOrder($courseId);
        return $result;
    }

    public function acl_listContentOrderAsHTML ($courseId)
    {
        ob_start();
        $view = new \Zend_View();
        $view->setBasePath(
                APPLICATION_PATH . DIRECTORY_SEPARATOR . "modules" .
                         DIRECTORY_SEPARATOR . "Site" . DIRECTORY_SEPARATOR .
                         "views");
        
        $course = $this->_service->find($courseId);
        if (! is_object($course)) {
            $course = $this->_service->findOneByTitle($courseId);
        }
        $view->sortedContent = $this->_service->acl_listContentOrder(
                $course->getId());
        if (\Zend_Auth::getInstance()->hasIdentity()) {
            $subscriptions = $this->_service->acl_findSubscriptions();
            if (is_array($subscriptions)) {
                foreach ($subscriptions as $key => $subscriptionObj) {
                    if ($subscriptionObj->getCourse()->getId() ==
                             $course->getId()) {
                        $view->page = $course->getTitle();
                        $view->completedContent = $subscriptionObj->getCompletedContent();
                        break;
                    }
                }
            }
        }
        
        echo $view->render('partials/curriculum-provider.phtml');
        return ob_get_clean();
    }

    /**
     *
     * @see \App\Service\iCourse::acl_providerUpdateContentSort()
     * @param integer $courseId            
     * @param array $contentIDsInChapterIDsArray            
     * @return array
     */
    public function acl_providerUpdateContentSort ($courseId, 
            $contentIDsInChapterIDsArray)
    {
        $result = $this->_service->acl_providerUpdateContentSort($courseId, 
                $contentIDsInChapterIDsArray);
        
        return $result;
    }

    /**
     *
     * @see \App\Service\iCourse::acl_getImageUrl()
     * @param integer $courseId,            
     * @param array $options            
     * @return string
     */
    public function acl_getImageUrl ($courseId, $options = array())
    {
        return $this->_service->acl_getImageUrl($courseId, $options);
    }
}