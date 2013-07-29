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
 * @Entity(repositoryClass="App\Repository\User")
 * @Table(name="user")
 */
class User
{

    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="string", name="username", unique=true)
     */
    private $username;

    /**
     * @Column(type="string", name="password")
     */
    private $password;

    /**
     * @Column(type="string", name="firstname")
     */
    private $firstname;

    /**
     * @Column(type="string", name="lastname")
     */
    private $lastname;

    /**
     * @Column(type="string", name="email", unique=true)
     */
    private $email;

    /**
     * @Column(type="string", name="role", nullable=true)
     */
    private $role;

    /**
     * @Column(type="boolean", name="activated")
     */
    private $activated;

    /**
     * @OneToMany(targetEntity="App\Entity\Subscription", mappedBy="_user",
     * cascade={"persist", "remove"})
     */
    private $_subscriptions;

    /**
     * @OneToMany(targetEntity="App\Entity\Course\Review", mappedBy="_user",
     * cascade={"persist", "remove"})
     */
    private $_reviews;

    /**
     * @OneToMany(targetEntity="App\Entity\Course\Announcement",
     * mappedBy="_user",
     * cascade={"persist", "remove"})
     */
    private $_announcements;


    /**
     * @OneToMany(targetEntity="App\Entity\Content\Question",
     * mappedBy="_user",
     * cascade={"persist", "remove"})
     */
    private $_contentQuestions;

    /**
     * @OneToMany(targetEntity="App\Entity\Content\Question\Answer",
     * mappedBy="_user",
     * cascade={"persist", "remove"})
     */
    private $_contentQuestionAnswers;

    /**
     * @OneToMany(targetEntity="App\Entity\Course\Question",
     * mappedBy="_user",
     * cascade={"persist", "remove"})
     */
    private $_courseQuestions;

    /**
     * @OneToMany(targetEntity="App\Entity\Course\Question\Answer",
     * mappedBy="_user",
     * cascade={"persist", "remove"})
     */
    private $_courseQuestionAnswers;


    public function __construct ()
    {
        $this->_subscriptions = new ArrayCollection();
        $this->_announcements = new ArrayCollection();
        $this->_coupons = new ArrayCollection();
        $this->_reviews = new ArrayCollection();
    }

    public function toArray ()
    {
        return get_object_vars($this);
    }

    public function getAnnouncements ()
    {
        return $this->_announcements;
    }

    public function setAnnouncements ($_announcements)
    {
        $this->_announcements = $_announcements;
    }

    public function getId ()
    {
        return $this->id;
    }

    public function getUsername ()
    {
        return $this->username;
    }

    public function getPassword ()
    {
        return $this->password;
    }

    public function getFirstname ()
    {
        return $this->firstname;
    }

    public function getLastname ()
    {
        return $this->lastname;
    }

    public function getEmail ()
    {
        return $this->email;
    }

    public function getRole ()
    {
        return $this->role;
    }

    public function getActivated ()
    {
        return $this->activated;
    }

    public function getCustomer ()
    {
        return $this->_customer;
    }

    public function getSubscriptions ()
    {
        return $this->_subscriptions;
    }

    public function getReviews ()
    {
        return $this->_reviews;
    }

    public function setUsername ($username)
    {
        $this->username = $username;
    }

    public function setPassword ($password)
    {
        $this->password = hash('sha512', $password);
    }

    public function setFirstname ($firstname)
    {
        $this->firstname = $firstname;
    }

    public function setLastname ($lastname)
    {
        $this->lastname = $lastname;
    }

    public function setEmail ($email)
    {
        $this->email = $email;
    }

    public function setRole ($role)
    {
        $this->role = $role;
    }

    public function setActivated ($activated)
    {
        $this->activated = $activated;
    }

    public function setSubscriptions ($_subscriptions)
    {
        $this->_subscriptions = $_subscriptions;
    }

    public function setReviews ($_reviews)
    {
        $this->_reviews = $_reviews;
    }
}