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
namespace App\Entity\Queue;

/**
 * @Entity(repositoryClass="App\Repository\Queue\Encoder")
 * @Table(name="queue_encoder")
 */
class Encoder {
	/**
	 * @Id @Column(type="integer", name="id")
	 * @GeneratedValue
	 */
	private $id;
	/**
	 * @Column(type="string", name="job_type")
	 */
	private $jobType;
	/**
	 * @Column(type="string", name="status", nullable=true)
	 */
	private $status;
	/**
	 * @Column(type="string", name="job_id")
	 */
	private $jobId;
	/**
	 * @Column(type="integer", name="percent_complete", nullable=true)
	 */
	private $percentComplete;
	/**
	 * @Column(type="string", name="error_message", nullable=true)
	 */
	private $errorMessage;
	/**
	 * @Column(type="integer", name="obj_id", nullable=true)
	 */
	private $objId;
	/**
	 * @Column(type="string", name="obj_type")
	 */
	private $objType;
	/**
	 * @Column(type="integer", name="duration_seconds", nullable=true)
	 */
	private $durationSeconds;
	
	/**
	 *
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 *
	 * @return the $jobType
	 */
	public function getJobType() {
		return $this->jobType;
	}
	
	/**
	 *
	 * @return the $status
	 */
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 *
	 * @return the $jobId
	 */
	public function getJobId() {
		return $this->jobId;
	}
	
	/**
	 *
	 * @return the $percentComplete
	 */
	public function getPercentComplete() {
		return $this->percentComplete;
	}
	
	/**
	 *
	 * @return the $errorMessage
	 */
	public function getErrorMessage() {
		return $this->errorMessage;
	}
	
	/**
	 *
	 * @return the $objId
	 */
	public function getObjId() {
		return $this->objId;
	}
	
	/**
	 *
	 * @return the $objType
	 */
	public function getObjType() {
		return $this->objType;
	}
	
	/**
	 *
	 * @return the $durationSeconds
	 */
	public function getDurationSeconds() {
		return $this->durationSeconds;
	}
	
	/**
	 *
	 * @param field_type $jobType        	
	 */
	public function setJobType($jobType) {
		$this->jobType = $jobType;
	}
	
	/**
	 *
	 * @param field_type $status        	
	 */
	public function setStatus($status) {
		$this->status = $status;
	}
	
	/**
	 *
	 * @param field_type $jobId        	
	 */
	public function setJobId($jobId) {
		$this->jobId = $jobId;
	}
	
	/**
	 *
	 * @param field_type $percentComplete        	
	 */
	public function setPercentComplete($percentComplete) {
		$this->percentComplete = $percentComplete;
	}
	
	/**
	 *
	 * @param field_type $errorMessage        	
	 */
	public function setErrorMessage($errorMessage) {
		$this->errorMessage = $errorMessage;
	}
	
	/**
	 *
	 * @param field_type $objId        	
	 */
	public function setObjId($objId) {
		$this->objId = $objId;
	}
	
	/**
	 *
	 * @param field_type $objType        	
	 */
	public function setObjType($objType) {
		$this->objType = $objType;
	}
	
	/**
	 *
	 * @param field_type $durationSeconds        	
	 */
	public function setDurationSeconds($durationSeconds) {
		$this->durationSeconds = $durationSeconds;
	}
	public function toArray() {
		return array (
				'id' => $this->getId (),
				'jobType' => $this->getJobType (),
				'status' => $this->getStatus (),
				'jobId' => $this->getJobId (),
				'percentComplete' => $this->getPercentComplete (),
				'errorMessage' => $this->getErrorMessage (),
				'objId' => $this->getObjId (),
				'objType' => $this->getObjType (),
				'durationSeconds' => $this->getDurationSeconds () 
		);
	}
}