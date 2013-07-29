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
use Doctrine\ORM\Events;

/**
 * @Entity(repositoryClass="App\Repository\Content\File")
 * @Table(name="content_file")
 */
class File extends \App\Entity\Content
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;

    public function getClass()
    {
        return __CLASS__;
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
                'chapter' => $this->getChapter(),
                'course' => $course,
                'isPublished' => $this->getIsPublished(),
                'role' => $this->getRole(),
                'title' => $this->getTitle(),
                'status' => $this->getStatus(),
                'description' => $this->getDescription(),
                'originalExtension' => $this->getOriginalExtension(),
                '__class__' => $this->getClass()
        );
    }
}