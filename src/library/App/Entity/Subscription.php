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

/**
 * @Entity(repositoryClass="App\Repository\Subscription")
 * @Table(name="subscription")
 */
class Subscription
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="string", name="role")
     */
    private $role;

    /**
     * @Column(type="boolean", name="is_enabled")
     */
    private $isEnabled;

    /**
     * @Column(type="array", name="completed_content")
     */
    private $completedContent;

    /**
     * @ManyToOne(targetEntity="App\Entity\Course",
     * inversedBy="_subscriptions")
     * @JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $_course;

    /**
     * @ManyToOne(targetEntity="App\Entity\User", inversedBy="_subscriptions")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $_user;

    /**
     * @OneToMany(targetEntity="App\Entity\Course\Assessment\Attempt",
     * mappedBy="_subscription",
     * cascade={"persist", "remove"})
     */
    private $_courseAssessmentAttempts;

    public function getId ()
    {
        return $this->id;
    }

    public function getRole ()
    {
        return $this->role;
    }

    public function getIsEnabled ()
    {
        return $this->isEnabled;
    }

    public function getCourse ()
    {
        return $this->_course;
    }

    public function getUser ()
    {
        return $this->_user;
    }

    public function setRole ($role)
    {
        $this->role = $role;
    }

    public function setIsEnabled ($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    public function setCourse ($_course)
    {
        $this->_course = $_course;
    }

    public function setUser ($_user)
    {
        $this->_user = $_user;
    }

    public function getCourseAssessmentAttempts ()
    {
        return $this->_courseAssessmentAttempts;
    }

    public function setCourseAssessmentAttempts ($_courseAssessmentAttempts)
    {
        $this->_courseAssessmentAttempts = $_courseAssessmentAttempts;
    }

    public function getCompletedContent ()
    {
        return $this->completedContent;
    }

    public function setCompletedContent ($completedContent)
    {
        $this->completedContent = $completedContent;
    }

    public function toArray ()
    {
        if (is_object($this->getCourse())) {
            $course = $this->getCourse()->getId();
        } else {
            $course = null;
        }
        if (is_object($this->getUser())) {
            $user = $this->getUser()->getId();
        } else {
            $user = null;
        }
        return array(
                'id' => $this->getId(),
                'role' => $this->getRole(),
                'isEnabled' => $this->getIsEnabled(),
                'course' => $course,
                'user' => $user
        );
    }
}