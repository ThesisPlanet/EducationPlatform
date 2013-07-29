<?php
namespace App\ServiceProxy;

class User extends aService
{

    protected function loadService ()
    {
        $this->_service = new \App\Service\User();
    }

    /**
     *
     * @param string $email
     * @param string $password
     * @return string
     */
    public function login ($email, $password)
    {
        if (\Zend_Auth::getInstance()->hasIdentity()) {
            return "Already authenticated";
        } else {
            if (\zend_registry::getInstance()->isRegistered('channel')) {
                $channel = \zend_registry::getInstance()->get('channel');
            } else {
                $channel = null;
            }
        }
        $form = new \App\Form\Auth\Login();

        if ($form->isValid(
                array(
                        'email' => $email,
                        'password' => $password
                ))) {
            $adapter = new \App\Auth\Adapter($form->getValue('email'),
                    $form->getValue('password'));
            $result = \Zend_Auth::getInstance()->authenticate($adapter);
            if (\Zend_Auth::getInstance()->hasIdentity()) {
                \Zend_Auth::getInstance()->getStorage()->write(
                        $result->getIdentity());
                return "Success";
            } else {
                throw new \exception(implode(' ', $result->getMessages()));
            }
        }
    }

    /**
     * Call to see if a user is currently authenticated.
     *
     * @return boolean
     */
    public function isAuthenticated ()
    {
        if (\Zend_Auth::getInstance()->hasIdentity() === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @return boolean
     */
    public function logout ()
    {
        \Zend_Auth::getInstance()->clearIdentity();
        \Zend_Session::destroy(true);
        return true;
    }

    /**
     *
     * @param integer $userId
     * @return array
     */
    public function acl_getProfile ($userId)
    {
        return $this->_service->acl_getProfile($userId);
    }
}