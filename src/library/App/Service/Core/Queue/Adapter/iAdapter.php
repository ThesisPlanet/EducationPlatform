<?php
namespace App\Service\Core\Queue\Adapter;
/**
 * An interchangeable queue management adapter system
 * @author Jack.Peterson
 *
 */
interface iAdapter
{

    public function task ($functionName, $workload);

    public function backgroundTask ($functionName, $workload);
}