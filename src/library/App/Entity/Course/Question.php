<?php
namespace App\Entity\Course;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="App\Repository\Course\Question")
 * @Table(name="course_question")
 */
class Question
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
     * @OneToMany(targetEntity="App\Entity\Course\Question\Answer",
     * mappedBy="_question",
     * cascade={"persist", "remove"})
     */
    private $_answers;

    /**
     * @ManyToOne(targetEntity="App\Entity\User", inversedBy="_courseQuestions")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $_user;

    /**
     * @ManyToOne(targetEntity="App\Entity\Course", inversedBy="_questions")
     * @JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $_course;

    public function __construct ()
    {
        $this->_answers = new ArrayCollection();
    }

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

    public function getAnswers ()
    {
        return $this->_answers;
    }

    public function getUser ()
    {
        return $this->_user;
    }

    public function getCourse ()
    {
        return $this->_course;
    }

    public function setText ($text)
    {
        $this->text = $text;
    }

    public function setCreatedAt ($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function setAnswers ($_answers)
    {
        $this->_answers = $_answers;
    }

    public function setUser ($_user)
    {
        $this->_user = $_user;
    }

    public function setCourse ($_course)
    {
        $this->_course = $_course;
    }

    public function toArray ()
    {
        $result = get_object_vars($this);

        $result['_course'] = $this->getCourse()->toArray();
        $result['_user'] = $this->getUser()->getId();
        $result['_username'] = $this->getUser()->getUsername();
        $result['_answerCount'] = count($this->getAnswers());
        $result['_age'] = \TP\Misc\TimeAgo::getAge($this->getCreatedAt());
        return $result;
    }
}