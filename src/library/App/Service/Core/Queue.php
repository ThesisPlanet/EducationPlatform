<?php
namespace App\Service\Core;

class Queue
{

    protected $_adapter;

    protected $_config;

    public function __construct (\Zend_Config $config)
    {
        $this->_config = $config;
        
        $adapterString = $config->adapter;
        if (class_exists($adapterString)) {
            $adapter = new $adapterString($config);
            $this->_adapter = $adapter;
        } else {
            throw new \exception(
                    "Queue adapter - \"$adapterString\" does not exist.");
        }
    }

    public function getConfig ()
    {
        return $this->_config;
    }

    public function getAdapter ()
    {
        return $this->_adapter;
    }

    public function setAdapter ($adapter)
    {
        $this->_adapter = $adapter;
    }

    /**
     * Do a task that must be ran realtime
     *
     * @param string $functionName            
     * @param array $workload            
     * @return mixed
     */
    public function task ($functionName, $workload)
    {
        return $this->_adapter->task($functionName, $workload);
    }

    /**
     * Do an asynchronous background task
     *
     * @param string $functionName            
     * @param array $workload            
     * @return mixed
     */
    public function backgroundTask ($functionName, $workload)
    {
        return $this->_adapter->backgroundTask($functionName, $workload);
    }
}