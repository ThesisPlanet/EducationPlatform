<?php
namespace App\Service\Core\Queue\Adapter;

/**
 * An interchangeable queue management adapter system
 *
 * @author Jack.Peterson
 */
class Dump extends aBase implements \App\Service\Core\Queue\Adapter\iAdapter
{
    /*
     * (non-PHPdoc) @see \App\Service\Core\Queue\Adapter\iAdapter::task()
     */
    public function task ($functionName, $workload)
    {
        echo "Received task \"$functionName\" with the following parameters:\n" .
                 print_r($workload, true) . "\n\n";
        return true;
    }
    
    /*
     * (non-PHPdoc) @see
     * \App\Service\Core\Queue\Adapter\iAdapter::backgroundTask()
     */
    public function backgroundTask ($functionName, $workload)
    {
        echo "Received background-task \"$functionName\" with the following parameters:\n" .
                 print_r($workload, true) . "\n\n";
        return true;
    }
}