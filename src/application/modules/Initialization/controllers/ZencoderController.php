<?php

class Initialization_ZencoderController extends Zend_Controller_Action
{

    public function init ()
    {}

    public function indexAction ()
    {
        $form = new \App\Form\Configuration\Zencoder();

        $configFile = CONFIGURATION_PATH . '/encoder.ini';
        if (! file_exists($configFile)) {
            $handle = fopen($configFile, "w+");
            $heading = "[" . APPLICATION_ENV . "]\nprovider = \"zencoder\"\n";
            fwrite($handle, $heading, strlen($heading));
            fclose($handle);
        }

        $config = new \Zend_Config_Ini($configFile, APPLICATION_ENV,
                array(
                        'allowModifications' => true
                ));

        if (isset($config->zencoder)) {
            $form->populate(
                    array(
                            'APIKey' => $config->zencoder->API_KEY
                    ));
        }

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {
                $config->zencoder = array();
                $config->provider = "zencoder";
                $config->zencoder->CANONICAL_ID = "6c8583d84664a381db0c6af0e79b285ede571885fbe768e7ea50e5d3760597dd";
                $config->zencoder->API_KEY = $form->getValue("APIKey");

                $writer = new \Zend_Config_Writer_Ini();
                $writer->setConfig($config);
                $writer->write($configFile, $config);
                $this->_redirect('/Initialization/');
            }
        }
        $this->view->form = $form;
    }
}

