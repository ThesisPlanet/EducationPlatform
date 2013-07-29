<?php
namespace App\Service\Core\Queue\Adapter;

/**
 * An interchangeable queue management adapter system
 *
 * @author Jack.Peterson
 */
abstract class aBase implements \App\Service\Core\Queue\Adapter\iAdapter
{

    const TASK_FAIL = "The Queue manager failed to execute the requested task.";

    const BACKGROUND_TASK_FAIL = "The Queue manager failed to execute the requested background task.";
}
