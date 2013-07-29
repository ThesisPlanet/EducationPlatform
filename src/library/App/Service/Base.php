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
namespace App\Service;

class Base
{

    const USER_NOT_SET = "I require a user valid user object to be provided";

    const USER_NOT_OBJECT = "The provided 'user' is not a valid object to work off of.";

    const USER_MUST_IMPLEMENT_GETSUBSCRIPTIONS = "The user object must provide the getSubscriptions method.";

    const COURSE_NOT_FOUND = "No course object is associated with this item.";

    const FORM_INVALID = "The provided information was invalid.";

    const PERMISSION_DENIED = "You do not have permission to perform that action.";

    const SERVER_NOT_DEFINED = "A Server address must be provided in order to locate that file.";

    const INVALID_PROTECTED_OBJECT = "The object requesting ACL validation does not implement a critical method. Aborted.";

    protected $_enable_caching;

    protected $_acl;

    private $_msg = null;
    
    public function setUser ($userObj)
    {
        if (is_object($userObj)) {
            $this->_user = $userObj;
        } else {
            throw new \exception(self::USER_NOT_OBJECT);
        }
    }

    protected $_messageTemplate = array(
            'invalid_pass' => 'The credentials entered were incorrect.',
            'form_errors' => 'Please fix the errors below, detailed in the form.',
            'contact' => 'Successfully sent message',
            'delete-log' => 'Successfully deleted log.',
            'update_success' => 'Updates successfully applied.',
            'create_success' => 'Successfully created.',
            'delete_success' => 'Deleted successfully',
            'invite_success' => "An invitation was created.",
            'invitation_processed' => "You are now subscribed. Please Log out and log back in again for the settings to take effect.",
            'forgot_password_success' => "a reset password e-mail should arrive shortly.",
            'password_changed' => "Your password has been changed. Go ahead and log in again.",
            'invalid_form' => "There was a problem processing your request. Please check that the required (bold) fields are filled out."
    );

    protected function _message ($key)
    {
        if (! key_exists($key, $this->_messageTemplate)) {
            throw new \exception('Message template key does not exist');
        }
        $this->_msg = $this->_messageTemplate[$key];
    }

    public function getMessage ()
    {
        return $this->_msg;
    }
}