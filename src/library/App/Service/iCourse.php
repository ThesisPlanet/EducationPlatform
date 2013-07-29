<?php
namespace App\Service;

interface iCourse
{

    public function find ($id);

    public function findAll ();

    public function findOneByTitle ($title);

    public function findByTopic ($topic);

    /**
     * Return the Form to add a new chapter to a course
     */
    public function getAddChapterForm ();

    /**
     * Users should be able to see all of their subscriptions
     *
     * @return array
     */
    public function acl_findSubscriptions ();

    /**
     * Fetches a single subscription object by courseId
     *
     * @param integer $courseId
     * @return object
     */
    public function acl_findSubscription ($courseId);

    /**
     * Adds a new chapter to a course.
     *
     * @param integer $courseId
     * @param string $text
     * @return integer
     */
    public function acl_addChapter ($courseId, $text);

    /**
     *
     * @param integer $chapterId
     * @return boolean;
     */
    public function acl_deleteChapter ($chapterId);

    /**
     * Returns an array of chapters in their respective sort order.
     *
     * @param integer $courseId
     */
    public function acl_listChapters ($courseId);

    /**
     * Update the order of chapters inside of a course.
     *
     * @param integer $courseId
     * @param array $chapterIDsArray
     * @return boolean
     */
    public function acl_updateChapterOrder ($courseId, $chapterIDsArray);

    /**
     * Users should be able to create new courses.
     *
     * @return integer
     * @param array $data
     */
    public function acl_create (array $data);

    /**
     * Providers should be able to update the topic, description, pricing, etc
     * related to their course.
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function acl_update ($id, array $data);

    /**
     * Administrators only can delete a course.
     *
     * @param integer $id
     */
    public function acl_delete ($id);

    /**
     * Course Owners should be able to publish a course once it is ready.
     *
     * @param integer $courseId
     */
    public function acl_publish ($courseId);

    /**
     * Course Owners should be able to unpublish a course in case they
     * accidentally published it.
     *
     * @param integer $courseId
     */
    public function acl_unpublish ($courseId);

    /**
     * Administrators (Employees) should be able to disable a course if there is
     * a problem with it.
     *
     * @param integer $courseId
     */
    public function acl_disable ($courseId);

    /**
     * Administrators (Employees) should be able to enable a course once the
     * problem has been resolved.
     *
     * @param integer $courseId
     */
    public function acl_enable ($courseId);

    /**
     * Subscribers should be able to add a review to a course.
     *
     * @param integer $courseId
     * @param boolean $isRecommended
     * @param string $reviewText
     */
    public function acl_addReview ($courseId, $isRecommended, $reviewText);

    /**
     * Users should be able to subscribe to a course.
     *
     * @param integer $courseId
     */
    public function acl_subscribe ($courseId);

    /**
     * Course providers should be able to approve subscription requests.
     * Applies to courses that have approval requirements
     *
     * @param integer $courseId
     * @param integer $userId
     */
    public function acl_approveSubscribeRequest ($courseId, $userId);

    /**
     * Course providers should be able to reject subscription requests.
     * Applies to courses that have approval requirements
     *
     * @param integer $courseId
     * @param integer $userId
     */
    public function acl_denySubscribeRequest ($courseId, $userId);

    /**
     * Subscribers should be able to unsubscribe from a course.
     *
     * @param integer $courseId
     */
    public function acl_unsubscribe ($courseId);

    /**
     * Users should be able to accept an invitation from a course provider
     *
     * @param integer $courseId
     * @param integer $userId
     */
    public function acl_userAcceptInvite ($courseId, $userId);

    /**
     * Users should be able to reject an invitation from a course provider
     *
     * @param integer $courseId
     * @param integer $userId
     */
    public function acl_userRejectInvite ($courseId, $userId);

    /**
     * Subscribers should be able to remove their review that they wrote about a
     * course.
     *
     * @param integer $reviewId
     */
    public function acl_removeReview ($reviewId);

    /**
     * Add a content ID to the array of completed content
     *
     * @param integer $courseId
     * @param integer $contentId
     */
    public function acl_completeContent ($courseId, $contentId);

    /**
     * Remove the content ID from the array of completed content
     *
     * @param integer $courseId
     * @param integer $contentId
     */
    public function acl_uncompleteContent ($courseId, $contentId);

    /**
     * return the percentage of completion for any given user's subscription for
     * a course
     *
     * @param integer $courseId
     * @return float
     */
    public function acl_getPercentComplete ($courseId);

    /**
     * return a list of completed content
     *
     * @param integer $courseId
     * @return array
     */
    public function acl_getCompletedContentList ($courseId);

    /**
     * Invite a user by email to a course
     *
     * @param integer $courseId
     * @param string $email
     * @return integer
     */
    public function acl_providerSubscribeUser ($courseId, $email);

    /**
     * Remove a subscription
     *
     * @param integer $subscriptionId
     * @return boolean
     */
    public function acl_providerDeleteSubscription ($subscriptionId);

    /**
     * give a subscriber a new role (subscriber/provider)
     *
     *
     * @param integer $subscriptionId
     * @param string $newRole
     * @return boolean
     */
    public function acl_providerUpdateSubscriptionRole ($subscriptionId,
            $newRole);

    /**
     *
     * @param integer $courseId
     * @param string $announcementText
     * @param boolean $shouldNotifyUsers
     * @return integer
     */
    public function acl_providerCreateAnnouncement ($courseId, $announcementText,
            $shouldNotifyUsers);

    /**
     *
     * @param integer $announcementId
     * @return boolean
     */
    public function acl_providerRemoveAnnouncement ($announcementId);

    /**
     *
     * @param course $courseId
     * @return array
     */
    public function acl_listAnnouncements ($courseId);

    /**
     *
     * @param integer $announcementId
     */
    public function acl_findAnnouncement ($announcementId);

    /**
     *
     * @param integer $courseId
     * @throws \exception
     * @return array
     */
    public function acl_listContentOrder ($courseId);

    /**
     *
     * @param integer $courseId
     * @param array $contentIDsInChapterIDsArray
     */
    public function acl_providerUpdateContentSort ($courseId,
            $contentIDsInChapterIDsArray);

    /**
     * Gets the URL for the course image
     *
     * @param integer $courseId
     * @param array $options
     * @return string
     */
    public function acl_getImageUrl ($courseId, $options = array());
}