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
namespace App\Entity\Course;

/**
 * @Entity(repositoryClass="App\Repository\Course\Review")
 * @Table(name="course_review")
 */
class Review
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="App\Entity\Course", inversedBy="_reviews")
     * @JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $_course;

    /**
     * @ManyToOne(targetEntity="App\Entity\User", inversedBy="_reviews")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $_user;

    /**
     * @Column(type="boolean", name="user_recommends")
     */
    private $userRecommends;

    /**
     * @Column(type="text", name="comment", nullable=true)
     */
    private $comment;

    public function getId ()
    {
        return $this->id;
    }

    public function getCourse ()
    {
        return $this->_course;
    }

    public function getUser ()
    {
        return $this->_user;
    }

    public function getUserRecommends ()
    {
        return $this->userRecommends;
    }

    public function getComment ()
    {
        return $this->comment;
    }

    public function setCourse ($_course)
    {
        $this->_course = $_course;
    }

    public function setUser ($_user)
    {
        $this->_user = $_user;
    }

    public function setUserRecommends ($userRecommends)
    {
        $this->userRecommends = $userRecommends;
    }

    public function setComment ($comment)
    {
        $this->comment = $comment;
    }
}