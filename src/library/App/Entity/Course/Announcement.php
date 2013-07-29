<?php
namespace App\Entity\Course;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="App\Repository\Course\Announcement")
 * @Table(name="course_announcement")
 */
class Announcement
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
     * @ManyToOne(targetEntity="App\Entity\User", inversedBy="_announcements")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $_user;

    /**
     * @ManyToOne(targetEntity="App\Entity\Course", inversedBy="_announcements")
     * @JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $_course;

    public function __construct ()
    {}

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
        return get_object_vars($this);
    }
}