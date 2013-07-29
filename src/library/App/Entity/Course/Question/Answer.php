<?php
namespace App\Entity\Course\Question;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="App\Repository\Course\Question\Answer")
 * @Table(name="course_question_answer")
 */
class Answer
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="text", name="text")
     */
    private $text;

    /**
     * @Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * @ManyToOne(targetEntity="App\Entity\Course\Question",
     * inversedBy="_answers")
     * @JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $_question;

    /**
     * @ManyToOne(targetEntity="App\Entity\User",
     * inversedBy="_courseQuestionAnswers")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $_user;

    public function getId ()
    {
        return $this->id;
    }

    public function getText ()
    {
        return $this->text;
    }

    public function getCreatedAt ()
    {
        return $this->createdAt;
    }

    public function getQuestion ()
    {
        return $this->_question;
    }

    public function getUser ()
    {
        return $this->_user;
    }

    public function setText ($text)
    {
        $this->text = $text;
    }

    public function setCreatedAt ($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function setQuestion ($_question)
    {
        $this->_question = $_question;
    }

    public function setUser ($_user)
    {
        $this->_user = $_user;
    }

    public function toArray ()
    {
        return array(
                'id' => $this->getId(),
                'createdAt' => $this->getCreatedAt(),
                'text' => $this->getText(),
                '_question' => $this->getQuestion()->toArray(),
                '_user' => $this->getUser()->getId(),
                '_username' => $this->getUser()->getUsername(),
                '_age' => \TP\Misc\TimeAgo::getAge($this->getCreatedAt())
        );
    }
}