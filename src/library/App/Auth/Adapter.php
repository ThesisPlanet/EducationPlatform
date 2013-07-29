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
namespace App\Auth;
class Adapter implements \Zend_Auth_Adapter_Interface
{
    const NOT_FOUND_MSG = "Account not found";
    const BAD_PASSWORD_MSG = "That password is incorrect";
    const NO_SUBSCRIPTION = "It appears that you do not have a subscription to this channel.";
    const NOT_ACTIVATED_MSG = "you must activate you account prior to signing in. Please check your inbox for an activation e-mail.";
    protected $identity;
    protected $password = "";
    protected $email = "";
    protected $channel = "";
    public function __construct ($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
        $this->identity = null;
    }
    public function authenticate ()
    {
        $userService = new \App\Service\User();
        try {
            $user = $userService->authenticate($this->email, $this->password);
        } catch (\exception $e) {
            if ($e->getMessage() == \App\Service\User::WRONG_PASSWORD) {
                return $this->createResult(
                \Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                array(self::BAD_PASSWORD_MSG));
            }
            if ($e->getMessage() == \App\Service\User::NOT_FOUND) {
                return $this->createResult(
                \Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                array(self::NOT_FOUND_MSG));
            }
            if ($e->getMessage() == \App\Service\User::NOT_ACTIVATED) {
                return $this->createResult(
                \Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                array(self::NOT_ACTIVATED_MSG));
            }
            throw new \exception($e->getMessage());
        }
        $this->identity = new \App\Auth\Identity($user->getId());
        return $this->createResult(\Zend_Auth_Result::SUCCESS);
    }
    private function getIdentity ()
    {
        return $this->identity;
    }
    private function createResult ($code, $messages = array())
    {
        if (! is_array($messages)) {
            $messages = array($messages);
        }
        return new \Zend_Auth_Result($code, $this->getIdentity(), $messages);
    }
}
