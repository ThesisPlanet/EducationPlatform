<?php
namespace App\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="App\Repository\Content\Question")
 * @Table(name="content_question")
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
     * @OneToMany(targetEntity="App\Entity\Content\Question\Answer",
     * mappedBy="_question",
     * cascade={"persist", "remove"})
     */
    private $_answers;

    /**
     * @ManyToOne(targetEntity="App\Entity\User", inversedBy="_contentQuestions")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $_user;

    /**
     * @ManyToOne(targetEntity="App\Entity\Content", inversedBy="_questions")
     * @JoinColumn(name="content_id", referencedColumnName="id")
     */
    private $_content;

    public function getContent ()
    {
        return $this->_content;
    }

    public function setContent ($_content)
    {
        $this->_content = $_content;
    }

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

    public function toArray ()
    {
        return get_object_vars($this);
    }
}