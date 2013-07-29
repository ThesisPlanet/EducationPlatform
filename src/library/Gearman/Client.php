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
namespace Gearman;

class Client
{

    protected $client;

    public function __construct ()
    {
        $this->client = new \GearmanClient();
        $this->client->addServer(
                \Zend_Registry::getInstance()->get('gearman')->server);
    }

    public function task ($functionName, $workload)
    {}

    public function backgroundTask ($functionName, $workload)
    {
        $workload = serialize($workload);
        $logger = \Zend_Registry::getInstance()->get('logger');
        
        $job_handle = $this->client->doBackground($functionName, $workload);
        $logger->log(
                "Job ID: " . $job_handle .
                         "\n Function: $functionName\n Workload: $workload\n", 
                        \Zend_Log::INFO);
        if ($this->client->returnCode() != GEARMAN_SUCCESS) {
            $logger->log($this->client->returnCode(), \Zend_Log::err);
            return false;
        } else {
            return true;
        }
    }
}