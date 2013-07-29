<?php

class Initialization_EmailController extends Zend_Controller_Action
{

    public function init ()
    {
        ini_set('max_execution_time', 120);
    }

    public function indexAction ()
    {
        $configFile = CONFIGURATION_PATH . '/communication.ini';

        if (! file_exists($configFile)) {
            $handle = fopen($configFile, "w+");
            $heading = "[" . APPLICATION_ENV . "]\n";
            fwrite($handle, $heading, strlen($heading));
            fclose($handle);
        }

        $config = new \Zend_Config_Ini($configFile, APPLICATION_ENV,
                array(
                        'allowModifications' => true
                ));

        $form = new \App\Form\Configuration\Email();

        if (isset($config->email) && isset($config->email->transport)) {
            $form->populate(
                    array(
                            'server' => $config->email->smtp->server,
                            'port' => $config->email->smtp->port,
                            'username' => $config->email->smtp->username,
                            'password' => $config->email->smtp->password,
                            'ssl' => $config->email->smtp->ssl,
                            'authMode' => $config->email->smtp->auth
                    ));
        }

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {
                $config->email = array();
                $config->email->transport = "smtp";
                $config->email->smtp = array();
                $config->email->smtp->server = $form->getValue('server');
                $config->email->smtp->port = $form->getValue('port');
                $config->email->smtp->username = $form->getValue('username');
                $config->email->smtp->password = $form->getValue('password');
                $config->email->smtp->ssl = $form->getValue('ssl');
                $config->email->smtp->auth = $form->getValue('authMode');
                $writer = new \Zend_Config_Writer_Ini();
                $writer->setConfig($config);

                $writer->write($configFile, $config);

                $this->_redirect('/Initialization/');
            }
        }
        $this->view->form = $form;
    }
}

