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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="App\Repository\Queue")
 * @Table(name="queue")
 */
class Queue
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="text", name="job")
     */
    private $job;

    /**
     * @Column(type="text", name="status")
     */
    private $status;

    /**
     * @Column(type="array", name="data")
     */
    private $data;

    /**
     * @Column(type="text", name="notes", nullable=true)
     */
    private $notes;

    public function __construct ()
    {}

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
     * @return the $job
     */
    public function getJob ()
    {
        return $this->job;
    }

    /**
     *
     * @return the $status
     */
    public function getStatus ()
    {
        return $this->status;
    }

    /**
     *
     * @return the $data
     */
    public function getData ()
    {
        return $this->data;
    }

    /**
     *
     * @return the $notes
     */
    public function getNotes ()
    {
        return $this->notes;
    }

    /**
     *
     * @param field_type $job
     */
    public function setJob ($job)
    {
        $this->job = $job;
    }

    /**
     *
     * @param field_type $status
     */
    public function setStatus ($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @param field_type $data
     */
    public function setData ($data)
    {
        $this->data = $data;
    }

    /**
     *
     * @param field_type $notes
     */
    public function setNotes ($notes)
    {
        $this->notes = $notes;
    }

    public function toArray ()
    {
        return get_object_vars($this);
    }
}