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
namespace App\Entity\Content;

/**
 * @Entity(repositoryClass="App\Repository\Content\Audio")
 * @Table(name="content_audio")
 */
class Audio extends \App\Entity\Content
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @Column(type="bigint", name="converted_size_kb", nullable=true)
     */
    private $convertedSizeKB;

    /**
     * @Column(type="integer", name="duration_in_seconds")
     */
    private $durationInSeconds = "0";

    public function getClass ()
    {
        return __CLASS__;
    }

    /**
     *
     * @return the $durationInSeconds
     */
    public function getDurationInSeconds ()
    {
        return $this->durationInSeconds;
    }

    /**
     *
     * @param field_type $durationInSeconds
     */
    public function setDurationInSeconds ($durationInSeconds)
    {
        $this->durationInSeconds = $durationInSeconds;
    }

    public function getConvertedSizeKB ()
    {
        return $this->convertedSizeKB;
    }

    public function setConvertedSizeKB ($convertedSizeKB)
    {
        $this->convertedSizeKB = $convertedSizeKB;
    }

    public function toArray ()
    {
        if (is_object($this->getCourse())) {
            $course = $this->getCourse()->getId();
        } else {
            $course = null;
        }
        return array(
                'id' => $this->getId(),
                'sortOrder' => $this->getSortOrder(),
                'originalSizeKB' => $this->getOriginalSizeKB(),
                'convertedSizeKB' => $this->getConvertedSizeKB(),
                'durationInSeconds' => $this->getDurationInSeconds(),
                'chapter' => $this->getChapter(),
                'course' => $course,
                'isPublished' => $this->getIsPublished(),
                'isEnabled' => $this->getIsEnabled(),
                'role' => $this->getRole(),
                'title' => $this->getTitle(),
                'status' => $this->getStatus(),
                'description' => $this->getDescription(),
                'originalExtension' => $this->getOriginalExtension(),
                '__class__' => $this->getClass()
        );
    }
}