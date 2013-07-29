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
 * @Entity(repositoryClass="App\Repository\Course\Assessment\Question")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"MultipleChoice" =
 * "App\Entity\Course\Assessment\Question\MultipleChoice",
 * "TrueFalse" = "App\Entity\Course\Assessment\Question\TrueFalse",
 * "FillInTheBlank" = "App\Entity\Course\Assessment\Question\FillInTheBlank"})
 * @Table(name="course_assessment_question")
 */
class Question
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="string", name="title")
     */
    private $title;

    /**
     * @ManyToOne(targetEntity="App\Entity\Course\Assessment",
     * inversedBy="_questions")
     * @JoinColumn(name="assessment_id", referencedColumnName="id")
     */
    private $_assessment;

    public function getId ()
    {
        return $this->id;
    }

    public function getTitle ()
    {
        return $this->title;
    }

    public function getAssessment ()
    {
        return $this->_assessment;
    }

    public function setTitle ($title)
    {
        $this->title = $title;
    }

    public function setAssessment ($_assessment)
    {
        $this->_assessment = $_assessment;
    }
}