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
namespace App\Entity\Subscription;
/**
 * @Entity(repositoryClass="App\Repository\Subscription\Approval")
 * @Table(name="subscription_approval")
 */
class Approval
{
    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;
    /**
     * @Column(type="string", name="email")
     */
    private $email;
    /**
     * @Column(type="boolean", name="user_approved")
     */
    private $userApproved;
    /**
     * @Column(type="boolean", name="course_approved")
     */
    private $courseApproved;
    /**
     * @Column(type="string", name="role")
     */
    private $role;
    /**
     * @Column(type="datetime", name="expires", nullable=true)
     */
    private $expires;
    /**
     * @Column(type="string", name="status", nullable=true)
     */
    private $status;
    /**
     * @ManyToOne(targetEntity="App\Entity\Course",
     * inversedBy="_subscriptions")
     * @JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $_course;
    /**
     * @ManyToOne(targetEntity="App\Entity\User", inversedBy="_subscriptions")
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $_user;
    public function getId ()
    {
        return $this->id;
    }
    public function getEmail ()
    {
        return $this->email;
    }
    public function getUserApproved ()
    {
        return $this->userApproved;
    }
    public function getCourseApproved ()
    {
        return $this->courseApproved;
    }
    public function getRole ()
    {
        return $this->role;
    }
    public function getExpires ()
    {
        return $this->expires;
    }
    public function getStatus ()
    {
        return $this->status;
    }
    public function getCourse ()
    {
        return $this->_course;
    }
    public function getUser ()
    {
        return $this->_user;
    }
    public function setEmail ($email)
    {
        $this->email = $email;
    }
    public function setUserApproved ($userApproved)
    {
        $this->userApproved = $userApproved;
    }
    public function setCourseApproved ($courseApproved)
    {
        $this->courseApproved = $courseApproved;
    }
    public function setRole ($role)
    {
        $this->role = $role;
    }
    public function setExpires ($expires)
    {
        $this->expires = $expires;
    }
    public function setStatus ($status)
    {
        $this->status = $status;
    }
    public function setCourse ($_course)
    {
        $this->_course = $_course;
    }
    public function setUser ($_user)
    {
        $this->_user = $_user;
    }
    public function toArray ()
    {
        if (is_object($this->_course)) {
            $course = $this->_course->getId();
        } else {
            $course = null;
        }
        if (is_object($this->_user)) {
            $user = $this->_user->getId();
        } else {
            $user = null;
        }
        return array('id' => $this->getId(), 'email' => $this->getEmail(), 
        'userApproved' => $this->getUserApproved(), 
        'courseApproved' => $this->getCourseApproved(), 
        'role' => $this->getRole(), 'expires' => $this->getExpires(), 
        'status' => $this->getStatus(), 'course' => $course, 'user' => $user);
    }
}