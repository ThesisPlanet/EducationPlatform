<?php
namespace App\Service;

interface iUser
{

    public function register (array $data);

    public function authenticate ($email, $password);

    public function authorize ($userId);

    public function acl_delete ($id);

    public function activate ($email, $token);

    public function sendRegistrationEmail ($userId, $options = array());

    public function findByEmail ($email);

    public function findOneByUsername ($username);

    public function find ($id);

    public function findAll ();

    public function getForm ();

    public function getForgotPasswordForm ();

    public function forgotPassword (array $data, $options = array());

    public function resetPassword (array $data);

    public function acl_adminResetPassword ($id, array $data);

    public function getResetPasswordForm ();

    public function validateResetPasswordToken ($user, $token);

    public function acl_adminEnableUserAccount ($userId);

    public function acl_adminDisableUserAccount ($userId);

    public function acl_getProfile ($userId);
}