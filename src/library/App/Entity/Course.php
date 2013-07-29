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
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="App\Repository\Course")
 * @Table(name="course")
 */
class Course
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="text", name="topic")
     */
    private $topic;

    /**
     * @Column(type="string", name="title")
     */
    private $title;

    /**
     * @Column(type="text", name="description")
     */
    private $description;

    /**
     * @Column(type="boolean", name="is_searchable")
     */
    private $isSearchable;

    /**
     * @Column(type="boolean", name="is_enabled")
     */
    private $isEnabled;

    /**
     * @Column(type="boolean", name="is_published")
     */
    private $isPublished;

    /**
     * @Column(type="boolean", name="is_approval_required")
     */
    private $isApprovalRequired;

    /**
     * @OneToMany(targetEntity="App\Entity\Subscription", mappedBy="_course",
     * cascade={"persist", "remove"})
     */
    private $_subscriptions;

    /**
     * @OneToMany(targetEntity="App\Entity\Content", mappedBy="_course")
     */
    private $_content;

    /**
     * @OneToMany(targetEntity="App\Entity\Course\Review", mappedBy="_course",
     * cascade={"persist", "remove"})
     */
    private $_reviews;

    /**
     * @OneToMany(targetEntity="App\Entity\Course\Question", mappedBy="_course",
     * cascade={"persist", "remove"})
     */
    private $_questions;

    /**
     * @OneToMany(targetEntity="App\Entity\Course\Chapter", mappedBy="_course",
     * cascade={"persist", "remove"})
     */
    private $_chapters;

    /**
     * @OneToMany(targetEntity="App\Entity\Course\Announcement",
     * mappedBy="_course",
     * cascade={"persist", "remove"})
     */
    private $_announcements;

    /**
     * @OneToMany(targetEntity="App\Entity\Course\Assessment",
     * mappedBy="_course",
     * cascade={"persist", "remove"})
     */
    private $_assessments;

    public function __construct ()
    {
        $this->_subscriptions = new ArrayCollection();
        $this->_content = new ArrayCollection();
        $this->_reviews = new ArrayCollection();
        $this->_questions = new ArrayCollection();
        $this->_chapters = new ArrayCollection();
        $this->_announcements = new ArrayCollection();
        $this->_assessments = new ArrayCollection();
    }

    public function toArray ()
    {
        return get_object_vars($this);
    }

    public function getAssessments ()
    {
        return $this->_assessments;
    }

    public function setAssessments ($_assessments)
    {
        $this->_assessments = $_assessments;
    }

    public function getAnnouncements ()
    {
        return $this->_announcements;
    }

    public function setAnnouncements ($_announcements)
    {
        $this->_announcements = $_announcements;
    }

    public function getQuestions ()
    {
        return $this->_questions;
    }

    public function setQuestions ($_questions)
    {
        $this->_questions = $_questions;
    }

    /**
     *
     * @return the $_chapters
     */
    public function getChapters ()
    {
        return $this->_chapters;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $_chapters
     */
    public function setChapters ($_chapters)
    {
        $this->_chapters = $_chapters;
    }

    public function getIsApprovalRequired ()
    {
        return $this->isApprovalRequired;
    }

    public function setIsApprovalRequired ($isApprovalRequired)
    {
        $this->isApprovalRequired = $isApprovalRequired;
    }

    public function getIsPublished ()
    {
        return $this->isPublished;
    }

    public function getReviews ()
    {
        return $this->_reviews;
    }

    public function setIsPublished ($isPublished)
    {
        $this->isPublished = $isPublished;
    }

    public function setReviews ($_reviews)
    {
        $this->_reviews = $_reviews;
    }

    public function getId ()
    {
        return $this->id;
    }

    public function getTopic ()
    {
        return $this->topic;
    }

    public function getTitle ()
    {
        return $this->title;
    }

    public function getDescription ()
    {
        return $this->description;
    }

    public function getPrice ()
    {
        return $this->price;
    }

    public function getIsSearchable ()
    {
        return $this->isSearchable;
    }

    public function getIsEnabled ()
    {
        return $this->isEnabled;
    }

    public function getSubscriptions ()
    {
        return $this->_subscriptions;
    }

    public function getContent ()
    {
        return $this->_content;
    }

    public function setContent ($_content)
    {
        $this->_content = $_content;
    }

    public function setTopic ($topic)
    {
        $this->topic = $topic;
    }

    public function setTitle ($title)
    {
        $this->title = $title;
    }

    public function setDescription ($description)
    {
        $this->description = $description;
    }

    public function setPrice ($price)
    {
        $this->price = $price;
    }

    public function setIsSearchable ($isSearchable)
    {
        $this->isSearchable = $isSearchable;
    }

    public function setIsEnabled ($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    public function setSubscriptions ($_subscriptions)
    {
        $this->_subscriptions = $_subscriptions;
    }
}