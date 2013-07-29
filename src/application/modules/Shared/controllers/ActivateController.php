<?php
class Shared_ActivateController extends Zend_Controller_Action
{
    public function accountAction ()
    {
        $this->view->email = $this->_request->getParam('email');
        try {
            $service = new \App\Service\User();
            $email = $this->_request->getParam('email');
            $token = $this->_request->getParam('token');
            if (isset($email)) {
                if (isset($token)) {
                    $activate = $service->activate(
                    $this->_request->getParam('email'), 
                    $this->_request->getParam('token'));
                    if ($activate == true) {
                        $this->_redirect('/activate/accountsuccess');
                    } else {
                        $this->view->ok = false;
                    }
                } else {
                    throw new \exception("A token must be provided.");
                }
            } else {
                throw new \exception("you must provide an e-mail address.");
            }
        } catch (\exception $e) {
            if ($e->getMessage() == \App\Service\User::NOT_FOUND)
            {
                $this->view->noAccount = true;
            }
        }
    }
    public function accountsuccessAction ()
    {}
}