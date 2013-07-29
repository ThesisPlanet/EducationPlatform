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
namespace Gearman\DEP\Utilization;
class AggregateMonthly
{
    public function calculateStorage (\GearmanJob $job)
    {
        $em = \Zend_Registry::get('em');
        $em->getConnection()->close();
        $em->getConnection()->connect();
        $workload = unserialize($job->workload());
        $aggregateService = new \App\Service\Utilization\Aggregate\Monthly();
        $serviceService = new \App\Service\Channel();
        $serviceList = $serviceService->findAll();
        foreach ($serviceList as $key => $obj) {
            echo "Calculating Average Daily Storage for Service ID: " .
             $obj->getId() . "\n";
            $aggregateService->allocateStorageByServiceId($obj->getId(),
            $workload['startDate'], $workload['endDate']);
        }
        return true;
    }
    public function allocateStreaming (\GearmanJob $job)
    {
        $em = \Zend_Registry::get('em');
        $em->getConnection()->close();
        $em->getConnection()->connect();
        $workload = unserialize($job->workload());
        $aggregateService = new \App\Service\Utilization\Aggregate\Monthly();
        $channelService = new \App\Service\Channel();
        $serviceList = $channelService->findAll();
        foreach ($serviceList as $key => $obj) {
            echo "allocating unpaid streaming records for service ID: " .
             $obj->getId() . "\n";
            $aggregateService->allocateStreamingByServiceId($obj->getId(),
            $workload['endDate']);
        }
        return true;
    }
    public function allocateDownload (\GearmanJob $job)
    {
        $em = \Zend_Registry::get('em');
        $em->getConnection()->close();
        $em->getConnection()->connect();
        $workload = unserialize($job->workload());
        $aggregateService = new \App\Service\Utilization\Aggregate\Monthly();
        $channelService = new \App\Service\Channel();
        $channelList = $channelService->findAll();
        foreach ($channelList as $key => $obj) {
            echo "allocating unpaid download records for service ID: " .
             $obj->getId() . "\n";
            $aggregateService->allocateDownloadByServiceId($obj->getId(),
            $workload['endDate']);
        }
        return true;
    }
    public function allocateEncodingTime (\GearmanJob $job)
    {
        $em = \Zend_Registry::get('em');
        $em->getConnection()->close();
        $em->getConnection()->connect();
        $workload = unserialize($job->workload());
        $aggregateService = new \App\Service\Utilization\Aggregate\Monthly();
        $serviceService = new \App\Service\Channel();
        $serviceList = $serviceService->fetchAll();
        foreach ($serviceList as $key => $obj) {
            echo "allocating unpaid download records for service ID: " .
             $obj->getId() . "\n";
            $aggregateService->allocateEncodingByServiceId($obj->getId(),
            $workload['endDate']);
        }
        return true;
    }
}