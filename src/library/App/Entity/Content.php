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
namespace App\Entity;

/**
 * @Entity(repositoryClass="App\Repository\Content")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"audio" = "App\Entity\Content\Audio",
 * "file" = "App\Entity\Content\File",
 * "video" = "App\Entity\Content\Video"})
 * @Table(name="content")
 */
class Content
{

    protected $_acl = null;

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
     * @Column(type="text", name="description")
     */
    private $description;

    /**
     * @Column(type="string", name="original_extension", nullable=true)
     */
    private $originalExtension;

    /**
     * @Column(type="boolean", name="is_enabled")
     */
    private $isEnabled;

    /**
     * @Column(type="boolean", name="is_published")
     */
    private $isPublished;

    /**
     * @Column(type="string", name="role")
     */
    private $role;

    /**
     * @Column(type="string", name="original_size_KB", nullable=true)
     */
    private $originalSizeKB;

    /**
     * @Column(type="string", name="status")
     */
    private $status;

    /**
     * @Column(type="text", name="error_message", nullable=true)
     */
    private $errorMessage;

    /**
     * @Column(type="datetime", name="deleted_at", nullable=true)
     */
    private $deletedAt;

    /**
     * @Column(type="datetime", name="cleaned_at", nullable=true)
     * Indicates when the file was cleaned off the cloud (should always occur
     * AFTER a file has been deleted by X number of days.
     */
    private $cleanedAt;

    /**
     * @Column(type="integer", name="sort_order", nullable=true)
     */
    private $sortOrder;

    /**
     * @ManyToOne(targetEntity="App\Entity\Course", inversedBy="_content")
     * @JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $_course;

    /**
     * @OneToMany(targetEntity="App\Entity\Content\Question",
     * mappedBy="_content",
     * cascade={"persist", "remove"})
     */
    private $_questions;

    /**
     * @ManyToOne(targetEntity="App\Entity\Course\Chapter",
     * inversedBy="_content")
     * @JoinColumn(name="chapter_id", referencedColumnName="id")
     */
    private $_chapter;

    /**
     *
     * @return the $sortOrder
     */
    public function getSortOrder ()
    {
        return $this->sortOrder;
    }

    /**
     *
     * @param field_type $sortOrder            
     */
    public function setSortOrder ($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    public function getCourse ()
    {
        return $this->_course;
    }

    public function setCourse ($_course)
    {
        $this->_course = $_course;
    }

    public function getChapter ()
    {
        return $this->_chapter;
    }

    public function setChapter ($chapter)
    {
        $this->_chapter = $chapter;
    }

    public function getQuestions ()
    {
        return $this->_questions;
    }

    public function setQuestions ($_questions)
    {
        $this->_questions = $_questions;
    }
    
    // Events
    // End of Events
    public function getCleanedAt ()
    {
        return $this->cleanedAt;
    }

    public function setCleanedAt ($cleanedAt)
    {
        $this->cleanedAt = $cleanedAt;
    }

    public function getId ()
    {
        return $this->id;
    }

    public function getDeletedAt ()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt ($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    public function getTitle ()
    {
        return $this->title;
    }

    public function getDescription ()
    {
        return $this->description;
    }

    public function getOriginalExtension ()
    {
        return $this->originalExtension;
    }

    public function getRole ()
    {
        return $this->role;
    }

    public function getOriginalSizeKB ()
    {
        return $this->originalSizeKB;
    }

    public function getStatus ()
    {
        return $this->status;
    }

    public function getErrorMessage ()
    {
        return $this->errorMessage;
    }

    public function setTitle ($title)
    {
        $this->title = $title;
    }

    public function setDescription ($description)
    {
        $this->description = $description;
    }

    public function setOriginalExtension ($originalExtension)
    {
        $this->originalExtension = $originalExtension;
    }

    public function setRole ($role)
    {
        $this->role = $role;
    }

    public function setOriginalSizeKB ($originalSizeKB)
    {
        $this->originalSizeKB = $originalSizeKB;
    }

    public function setStatus ($status)
    {
        $this->status = $status;
    }

    public function setErrorMessage ($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function getIsPublished ()
    {
        return $this->isPublished;
    }

    public function setIsPublished ($isPublished)
    {
        $this->isPublished = $isPublished;
    }

    public function getIsEnabled ()
    {
        return $this->isEnabled;
    }

    public function setIsEnabled ($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     * creates and returns an ACL
     */
    public function getAcl ()
    {
        $aclGenerator = new \App\ContentAcl\Generator();
        $aclGenerator->create($this);
        $this->_acl = $aclGenerator->getAcl();
        return $this->_acl;
    }
}