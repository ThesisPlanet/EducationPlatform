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
namespace App\Entity\Course\Assessment;

/**
 * @Entity(repositoryClass="App\Repository\Course\Assessment\Attempt")
 * @Table(name="course_assessment_attempt")
 */
class Attempt
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="App\Entity\Course\Assessment",
     * inversedBy="_attempts")
     * @JoinColumn(name="assessment_id", referencedColumnName="id")
     */
    private $_assessment;

    /**
     * @ManyToOne(targetEntity="App\Entity\Subscription",
     * inversedBy="_courseAssessmentAttempts")
     * @JoinColumn(name="subscription_id", referencedColumnName="id")
     */
    private $_subscription;

    /**
     * @Column(type="datetime",name="started_at")
     */
    private $startedAt;

    /**
     * @Column(type="datetime",name="finished_at", nullable=true)
     */
    private $finishedAt;

    /**
     * @Column(type="array", name="questions", nullable=true)
     */
    private $questions;

    /**
     * @Column(type="array", name="answers", nullable=true)
     */
    private $answers;

    /**
     * @Column(type="decimal", name="score", nullable=true, precision=5,
     * scale=2)
     */
    private $score;

    /**
     * @Column(type="array", name="corrected", nullable=true)
     */
    private $corrected;

    public function getCorrected ()
    {
        return $this->corrected;
    }

    public function setCorrected ($corrected)
    {
        $this->corrected = $corrected;
    }

    public function getQuestions ()
    {
        return $this->questions;
    }

    public function setQuestions ($questions)
    {
        $this->questions = $questions;
    }

    public function getId ()
    {
        return $this->id;
    }

    public function getAssessment ()
    {
        return $this->_assessment;
    }

    public function getSubscription ()
    {
        return $this->_subscription;
    }

    public function getStartedAt ()
    {
        return $this->startedAt;
    }

    public function getFinishedAt ()
    {
        return $this->finishedAt;
    }

    public function getAnswers ()
    {
        return $this->answers;
    }

    public function getScore ()
    {
        return $this->score;
    }

    public function setAssessment ($_assessment)
    {
        $this->_assessment = $_assessment;
    }

    public function setSubscription ($_subscription)
    {
        $this->_subscription = $_subscription;
    }

    public function setStartedAt ($startedAt)
    {
        $this->startedAt = $startedAt;
    }

    public function setFinishedAt ($finishedAt)
    {
        $this->finishedAt = $finishedAt;
    }

    public function setAnswers ($answers)
    {
        $this->answers = $answers;
    }

    public function setScore ($score)
    {
        $this->score = $score;
    }

    public function toArray ()
    {
        return get_object_vars($this);
    }
}