<?php

class Initialization_AwsController extends Zend_Controller_Action
{

    public function init ()
    {}

    public function indexAction ()
    {
        $form = new \App\Form\Configuration\AWS();
        $configFile = CONFIGURATION_PATH . '/cloud.ini';
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

        if (isset($config->aws)) {
            $form->populate(
                    array(

                            'bucket' => $config->aws->BUCKET,
                            'key' => $config->aws->KEY,
                            'secretKey' => $config->aws->SECRET_KEY,
                            'accountId' => $config->aws->ACCOUNT_ID,
                            'canonicalId' => $config->aws->CANONICAL_ID,
                            'canonicalName' => $config->aws->CANONICAL_NAME,
                            'CFStreamingDistributionId' => $config->aws->CLOUDFRONT_STREAMING_DISTRIBUTION_ID,
                            'CFDownloadDistributionId' => $config->aws->CLOUDFRONT_DOWNLOAD_DISTRIBUTION_ID,
                            'CFKeypairId' => $config->aws->CLOUDFRONT_KEYPAIR_ID,
                            'CFPrivateKeyPEM' => $config->aws->CLOUDFRONT_PRIVATE_KEY_PEM,
                            'CFCanonicalId' => $config->aws->CLOUDFRONT_CANONICAL_ID,
                            'CFDistributionStreamer' => $config->aws->CLOUDFRONT_DISTRIBUTION_STREAMER,
                            'CFDownloadDistributionURL' => $config->aws->CLOUDFRONT_DOWNLOAD_DISTRIBUTION_URL
                    ));
        } else {
            $config->aws = array();
        }
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {
                $config->provider = "aws";
                $config->aws->BUCKET = $form->getValue('bucket');
                $config->aws->KEY = $form->getValue('key');
                $config->aws->SECRET_KEY = $form->getValue('secretKey');
                $config->aws->ACCOUNT_ID = $form->getValue('accountId');
                $config->aws->CANONICAL_ID = $form->getValue('canonicalId');
                $config->aws->CANONICAL_NAME = $form->getValue('canonicalName');
                $config->aws->CLOUDFRONT_STREAMING_DISTRIBUTION_ID = $form->getValue(
                        'CFStreamingDistributionId');
                $config->aws->CLOUDFRONT_DOWNLOAD_DISTRIBUTION_ID = $form->getValue(
                        'CFDownloadDistributionId');
                $config->aws->CLOUDFRONT_KEYPAIR_ID = $form->getValue(
                        'CFKeypairId');

                $config->aws->CLOUDFRONT_PRIVATE_KEY_PEM = $form->getValue(
                        'CFPrivateKeyPEM');
                $config->aws->CLOUDFRONT_CANONICAL_ID = $form->getValue(
                        'CFCanonicalId');
                $config->aws->CLOUDFRONT_DISTRIBUTION_STREAMER = $form->getValue(
                        'CFDistributionStreamer');
                $config->aws->ENABLE_EXTENSIONS = false;
                $config->aws->CLOUDFRONT_DOWNLOAD_DISTRIBUTION_URL = $form->getValue(
                        'CFDownloadDistributionURL');

                $writer = new \Zend_Config_Writer_Ini();
                $writer->setConfig($config);
                $writer->write($configFile, $config);

                $this->_redirect('/Initialization/');
            }
        }

        $this->view->form = $form;
    }
}

