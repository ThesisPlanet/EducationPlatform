<?php

class Initialization_UserController extends Zend_Controller_Action
{

    public function init ()
    {}

    public function indexAction ()
    {
        $configFile = CONFIGURATION_PATH . '/users.ini';

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

        $form = new \App\Form\Configuration\Users();

        if (isset($config->canRegister)) {
            $form->populate(
                    array(
                            'canRegister' => $config->canRegister,
                            'canCreateCourses' => $config->canCreateCourses
                    ));
        }

        $this->view->page = "Initialization - User preferences";
        if ($this->_request->isPost()) {

            if ($form->isValid($this->_request->getParams())) {
                $config->canRegister = $form->getValue('canRegister');
                $config->canCreateCourses = $form->getValue('canCreateCourses');
                $writer = new \Zend_Config_Writer_Ini();
                $writer->setConfig($config);
                $writer->write($configFile, $config);
                $this->_redirect('/Initialization/');
            }
        }
        $this->view->form = $form;
    }
}

