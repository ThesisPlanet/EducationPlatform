<?php
namespace App\Service\Core\Queue\Adapter;

/**
 * An interchangeable queue management adapter system
 *
 * @author Jack.Peterson
 */
class PHPDaemon extends aBase implements \App\Service\Core\Queue\Adapter\iAdapter
{

    protected $client;

    public function __construct (\Zend_Config $config)
    {
        $this->client = new \GearmanClient();
        foreach ($config->Gearman->server as $key => $server) {
            $this->client->addServer($server);
        }
    }

    /*
     * (non-PHPdoc) @see \App\Service\Core\Queue\Adapter\iAdapter::task()
     */
    public function task ($functionName, $workload)
    {
        $workload = serialize($workload);
        $logger = \Zend_Registry::getInstance()->get('logger');
        $result = $this->client->doNormal($functionName, $workload);
        if ($this->client->returnCode() != GEARMAN_SUCCESS) {
            $logger->log(
                    "Gearman Return code: " . $this->client->returnCode() .
                             " for function $functionName.", \Zend_Log::err);
            throw new \exception(self::TASK_FAIL);
        } else {
            return $result;
        }
    }

    /*
     * (non-PHPdoc) @see
     * \App\Service\Core\Queue\Adapter\iAdapter::backgroundTask()
     */
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
            throw new \exception(self::BACKGROUND_TASK_FAIL);
        } else {
            return true;
        }

        return true;
    }
}
