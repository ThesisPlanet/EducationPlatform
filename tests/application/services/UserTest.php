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

/**
 * User service is designed to be internal (NOT FOR API).
 * It's critical to the rest of the application, and therefore must be unit
 * tested.
 *
 * @author Jack.Peterson
 *        
 */
namespace tests\application\services;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class UserTest extends \Zend_Test_PHPUnit_ControllerTestCase
{

    protected $_service = null;

    protected $_userService = null;

    public function setUp ()
    {
        $this->_service = new \App\Service\User();
    }

    /**
     * Creates a new account.
     * Sends a registration Email Mock.
     * Activates from the Email mock.
     * Checks role to be a 'user'.
     * Deletes account.
     */
    public function testRegistrationFlow ()
    {
        $registrationData = array(
                'firstname' => "Test",
                'lastname' => "User",
                'email' => "joe@bob.com",
                'username' => "testInstance",
                'password' => "testPassword"
        );
        
        $uid = $this->_service->register($registrationData);
        
        $this->assertInternalType('integer', $uid);
        
        $user = $this->_service->find($uid);
        
        $correctToken = hash('sha512', 
                $user->getId() . $user->getEmail() . $user->getUsername());
        
        $this->AssertTrue(
                $this->_service->activate($user->getEmail(), $correctToken));
        
        $adminUser = $this->_service->findByEmail("testadmin@thesisplanet.com");
        $this->_service->setUser($adminUser);
        $this->assertTrue($this->_service->acl_delete($uid));
    }

    public function testFindAll ()
    {
        $this->assertInternalType('array', $this->_service->findAll());
    }

    public function testGetForgotPasswordForm ()
    {
        $this->assertInstanceOf('\App\Form\User\ForgotPassword', 
                $this->_service->getForgotPasswordForm());
    }

    public function testGetResetPasswordForm ()
    {
        $this->assertInstanceOf('\App\Form\User\ResetPassword', 
                $this->_service->getResetPasswordForm());
    }

    public function testResetPassword ()
    {
        $user = $this->_service->findByEmail("testuser@thesisplanet.com");
        
        $token = hash('sha512', 
                $user->getId() . $user->getEmail() . $user->getUsername() .
                         $user->getPassword());
        
        $this->assertTrue(
                $this->_service->resetPassword(
                        array(
                                'email' => "testuser@thesisplanet.com",
                                'token' => $token,
                                'password1' => "123",
                                'password2' => "123"
                        )));
    }

    public function testAdminResetPasswordUnauthorized ()
    {
        $user = $this->_service->findByEmail("testuser@thesisplanet.com");
        $this->_service->setUser($user);
        
        $this->setExpectedException("exception", 
                \App\Service\User::PERMISSION_DENIED);
        
        $this->_service->acl_adminResetPassword($user->getId(), 
                array(
                        'password1' => "abc",
                        'password2' => "abc"
                ));
    }

    public function testAdminResetPassword ()
    {
        $user = $this->_service->findByEmail("testadmin@thesisplanet.com");
        $this->_service->setUser($user);
        
        $this->assertTrue(
                $this->_service->acl_adminResetPassword($user->getId(), 
                        array(
                                'password1' => "abc",
                                'password2' => "abc"
                        )));
    }

    public function testAdminResetPasswordNoObject ()
    {
        $user = $this->_service->findByEmail("testadmin@thesisplanet.com");
        $this->_service->setUser($user);
        
        $this->setExpectedException("exception", \App\Service\User::NOT_FOUND);
        
        $this->_service->acl_adminResetPassword("0", 
                array(
                        'password1' => "abc",
                        'password2' => "abc"
                ));
    }

    public function testAdminResetPasswordInvalidForm ()
    {
        $user = $this->_service->findByEmail("testadmin@thesisplanet.com");
        $this->_service->setUser($user);
        $this->setExpectedException("exception", 
                \App\Service\User::FORM_INVALID);
        
        $this->_service->acl_adminResetPassword($user->getId(), 
                array(
                        'password1' => "abc",
                        'password2' => "def"
                ));
    }

    public function tearDown ()
    {
        unset($this->_service);
    }
}