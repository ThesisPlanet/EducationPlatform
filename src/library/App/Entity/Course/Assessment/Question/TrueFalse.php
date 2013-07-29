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
namespace App\Entity\Course\Assessment\Question;

/**
 * @Entity(repositoryClass="App\Repository\Course\Assessment\Question\TrueFalse")
 * @Table(name="course_assessment_question_truefalse")
 */
class TrueFalse extends \App\Entity\Course\Assessment\Question
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @Column(type="boolean", name="answer")
     */
    private $answer;

    public function getAnswer ()
    {
        return $this->answer;
    }

    public function setAnswer ($answer)
    {
        $this->answer = $answer;
    }

    public function getClass ()
    {
        return __CLASS__;
    }

    public function toArray ()
    {
        $out = get_object_vars($this);
        $out['id'] = $this->getId();
        $out['title'] = $this->getTitle();
        $out['class'] = $this->getClass();
        return $out;
    }
}