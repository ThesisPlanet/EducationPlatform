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
namespace App\Entity\Monitoring;
/**
 * @Entity(repositoryClass="App\Repository\Monitoring\Event")
 * @Table(name="monitoring_event")
 */
class Event
{
    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;
    /** @Column(type="string", name="device_name") */
    private $deviceName;
    /** @Column(type="string", name="device_ip") */
    private $deviceIP;
    /** @Column(type="text", name="message") */
    private $message;
    /** @Column(type="string", name="priority") */
    private $priority;
    /** @Column(type="string", name="priority_name") */
    private $priorityName;
    /** @Column(type="datetime", name="timestamp") */
    private $timestamp;
    public function getId ()
    {
        return $this->id;
    }
    public function getDeviceName ()
    {
        return $this->deviceName;
    }
    public function getDeviceIP ()
    {
        return $this->deviceIP;
    }
    public function getMessage ()
    {
        return $this->message;
    }
    public function getPriority ()
    {
        return $this->priority;
    }
    public function getPriorityName ()
    {
        return $this->priorityName;
    }
    public function getTimestamp ()
    {
        return $this->timestamp;
    }
    public function setDeviceName ($deviceName)
    {
        $this->deviceName = $deviceName;
    }
    public function setDeviceIP ($deviceIP)
    {
        $this->deviceIP = $deviceIP;
    }
    public function setMessage ($message)
    {
        $this->message = $message;
    }
    public function setPriority ($priority)
    {
        $this->priority = $priority;
    }
    public function setPriorityName ($priorityName)
    {
        $this->priorityName = $priorityName;
    }
    public function setTimestamp ($timestamp)
    {
        $this->timestamp = $timestamp;
    }
    public function toArray ()
    {
        if (is_object($this->getCategory())) {
            $category = $this->getCategory()->getId();
        } else {
            $category = null;
        }
        if (is_object($this->getChannel())) {
            $channel = $this->getChannel()->getId();
        } else {
            $channel = null;
        }
        return array('id' => $this->getId(), 
        'deviceName' => $this->getDeviceName(), 
        'deviceIP' => $this->getDeviceIP(), 'message' => $this->getMessage(), 
        'priority' => $this->getPriority(), 
        'priorityName' => $this->getPriorityName(), 
        'timestamp' => $this->getTimestamp());
    }
}