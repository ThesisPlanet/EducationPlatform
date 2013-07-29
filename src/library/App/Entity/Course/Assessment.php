<?php
namespace App\Entity\Course;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="App\Repository\Course\Assessment")
 * @Table(name="course_assessment")
 */
class Assessment
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="text", name="title")
     */
    private $title;

    /**
     * @Column(type="text", name="description")
     */
    private $description;

    /**
     * @Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * @Column(type="boolean", name="is_retake_allowed")
     */
    private $isRetakeAllowed;

    /**
     * @ManyToOne(targetEntity="App\Entity\Course", inversedBy="_assessments")
     * @JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $_course;

    /**
     * @oneToMany(targetEntity="App\Entity\Course\Assessment\Question",
     * mappedBy="_assessment",
     * cascade={"persist", "remove"})
     */
    private $_questions;

    /**
     * @oneToMany(targetEntity="App\Entity\Course\Assessment\Attempt",
     * mappedBy="_assessment",
     * cascade={"persist", "remove"})
     */
    private $_attempts;

    /**
     * @ManyToOne(targetEntity="App\Entity\Course\Chapter",
     * inversedBy="_assessments")
     * @JoinColumn(name="chapter_id", referencedColumnName="id")
     */
    private $_chapter;

    /**
     * @Column(type="integer", name="sort_order", nullable=true)
     */
    private $sortOrder;

    public function getChapter ()
    {
        return $this->_chapter;
    }

    public function getSortOrder ()
    {
        return $this->sortOrder;
    }

    public function setChapter ($_chapter)
    {
        $this->_chapter = $_chapter;
    }

    public function setSortOrder ($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    public function __construct ()
    {
        $this->_questions = new ArrayCollection();
        $this->_attempts = new ArrayCollection();
    }

    public function getId ()
    {
        return $this->id;
    }

    public function getTitle ()
    {
        return $this->title;
    }

    public function getDescription ()
    {
        return $this->description;
    }

    public function getCreatedAt ()
    {
        return $this->createdAt;
    }

    public function getIsRetakeAllowed ()
    {
        return $this->isRetakeAllowed;
    }

    public function getCourse ()
    {
        return $this->_course;
    }

    public function getQuestions ()
    {
        return $this->_questions;
    }

    public function getAttempts ()
    {
        return $this->_attempts;
    }

    public function setTitle ($title)
    {
        $this->title = $title;
    }

    public function setDescription ($description)
    {
        $this->description = $description;
    }

    public function setCreatedAt ($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function setIsRetakeAllowed ($isRetakeAllowed)
    {
        $this->isRetakeAllowed = $isRetakeAllowed;
    }

    public function setCourse ($_course)
    {
        $this->_course = $_course;
    }

    public function setQuestions ($_questions)
    {
        $this->_questions = $_questions;
    }

    public function setAttempts ($_attempts)
    {
        $this->_attempts = $_attempts;
    }

    public function getClass ()
    {
        return __CLASS__;
    }

    public function toArray ()
    {
        $outArr = get_object_vars($this);
        
        $outArr['__class__'] = $this->getClass();
        
        return $outArr;
    }
}