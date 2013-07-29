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
 * @Entity(repositoryClass="App\Repository\Configuration")
 * @Table(name="configuration")
 */
class Configuration
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="text", name="key")
     */
    private $key;

    /**
     * @Column(type="text", name="value")
     */
    private $value;

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
     * @return the $key
     */
    public function getKey ()
    {
        return $this->key;
    }

    /**
     *
     * @return the $value
     */
    public function getValue ()
    {
        return $this->value;
    }

    /**
     *
     * @param field_type $key
     */
    public function setKey ($key)
    {
        $this->key = $key;
    }

    /**
     *
     * @param field_type $value
     */
    public function setValue ($value)
    {
        $this->value = $value;
    }

    public function __construct ()
    {}

    public function toArray ()
    {
        return get_object_vars($this);
    }
}