<?php
namespace App\Entity\Course;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="App\Repository\Course\Chapter")
 * @Table(name="course_chapter")
 */
class Chapter
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="string", name="text")
     */
    private $text;

    /**
     * @Column(type="integer", name="priority", nullable=true)
     */
    private $priority;

    /**
     * @OneToMany(targetEntity="App\Entity\Content", mappedBy="_chapter")
     */
    private $_content;

    /**
     * @OneToMany(targetEntity="App\Entity\Course\Assessment", mappedBy="_chapter")
     */
    private $_assessments;

    /**
     * @ManyToOne(targetEntity="App\Entity\Course", inversedBy="_chapters")
     * @JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $_course;

    public function getAssessments ()
    {
        return $this->_assessments;
    }

    public function setAssessments ($_assessments)
    {
        $this->_assessments = $_assessments;
    }

    public function __construct ()
    {
        $this->_content = new ArrayCollection();
    }

    /**
     *
     * @return the $id
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     *
     * @return the $text
     */
    public function getText ()
    {
        return $this->text;
    }

    /**
     *
     * @return the $priority
     */
    public function getPriority ()
    {
        return $this->priority;
    }

    /**
     *
     * @return the $_content
     */
    public function getContent ()
    {
        return $this->_content;
    }

    /**
     *
     * @return the $_course
     */
    public function getCourse ()
    {
        return $this->_course;
    }

    /**
     *
     * @param field_type $text            
     */
    public function setText ($text)
    {
        $this->text = $text;
    }

    /**
     *
     * @param field_type $priority            
     */
    public function setPriority ($priority)
    {
        $this->priority = $priority;
    }

    /**
     *
     * @param field_type $_content            
     */
    public function setContent ($_content)
    {
        $this->_content = $_content;
    }

    /**
     *
     * @param field_type $_course            
     */
    public function setCourse ($_course)
    {
        $this->_course = $_course;
    }

    public function toArray ()
    {
        $contentArr = $this->getContent();
        $contentArrOut = array();
        if (count($contentArr) > 0) {
            foreach ($contentArr as $key => $contentObj) {
                $contentArrOut[$key] = $contentObj->getId();
            }
        }
        $result = get_object_vars($this);
        $result['_content'] = $contentArrOut;
        $result['priority'] = $this->getPriority();
        $result['text'] = $this->getText();
        $result['_contentCount'] = count($this->getContent());
        return $result;
    }
}