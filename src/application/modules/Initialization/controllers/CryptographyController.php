<?php

class Initialization_CryptographyController extends Zend_Controller_Action
{

    public function init ()
    {
        ini_set('max_execution_time', 120);
    }

    public function indexAction ()
    {
        $config = new \Zend_Config_Ini(
                APPLICATION_PATH . '/configs/communication.ini', APPLICATION_ENV,
                array(
                        'allowModifications' => true
                ));

        $form = new \App\Form\Configuration\Email();

        $form->populate(
                array(
                        'server' => $config->email->smtp->server,
                        'port' => $config->email->smtp->port,
                        'username' => $config->email->smtp->username,
                        'password' => $config->email->smtp->password,
                        'ssl' => $config->email->smtp->ssl,
                        'authMode' => $config->email->smtp->auth
                ));

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {
                $config->email->smtp->server = $form->getValue('server');
                $config->email->smtp->port = $form->getValue('port');
                $config->email->smtp->username = $form->getValue('username');
                $config->email->smtp->password = $form->getValue('password');
                $config->email->smtp->ssl = $form->getValue('ssl');
                $config->email->smtp->auth = $form->getValue('authMode');
                $writer = new \Zend_Config_Writer_Ini();
                $writer->setConfig($config);

                $writer->write(APPLICATION_PATH . '/configs/communication.ini',
                        $config);

                $this->_redirect('/Initialization/');
            }
        }
        $this->view->form = $form;
    }
}

