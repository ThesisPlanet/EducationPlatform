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
namespace Gearman\DEP;
class billing
{
    public function CalculateVideoStorageUsage (\GearmanJob $job)
    {
        $em = \Zend_Registry::get('em');
        $em->getConnection()->close();
        $em->getConnection()->connect();
        $logger = \Zend_Registry::getInstance()->get('logger');
        $workload = unserialize($job->workload());
        $service = new \App\Service\Utilization\Storage();
        $serviceId = $workload;
        $logger->log(
        "Calculating Daily video storage allocation for service Id: $workload.", 
        \Zend_Log::INFO);
        try {
            $result = $service->calculateVideoStorageUsage($serviceId);
            if ($result) {
                return true;
            } else {
                return false;
            }
        } catch (\exception $e) {
            $logger = \Zend_Registry::getInstance()->get('logger');
            $logger->log($e->getMessage(), \Zend_Log::ERR);
        }
    }
    public function CalculateAudioStorageUsage (\GearmanJob $job)
    {
        $em = \Zend_Registry::get('em');
        $em->getConnection()->close();
        $em->getConnection()->connect();
        $logger = \Zend_Registry::getInstance()->get('logger');
        $workload = unserialize($job->workload());
        $service = new \App\Service\Utilization\Storage();
        $serviceId = $workload;
        $logger->log(
        "Calculating Daily audio storage allocation for service Id: $workload.", 
        \Zend_Log::INFO);
        try {
            $result = $service->calculateAudioStorageUsage($serviceId);
            if ($result) {
                return true;
            } else {
                return false;
            }
        } catch (\exception $e) {
            echo $e->getMessage();
            $logger = \Zend_Registry::getInstance()->get('logger');
            $logger->log($e->getMessage(), \Zend_Log::ERR);
        }
    }
    public function CalculateFileStorageUsage (\GearmanJob $job)
    {
        $em = \Zend_Registry::get('em');
        $em->getConnection()->close();
        $em->getConnection()->connect();
        $logger = \Zend_Registry::getInstance()->get('logger');
        $workload = unserialize($job->workload());
        $service = new \App\Service\Utilization\Storage();
        $serviceId = $workload;
        $logger->log(
        "Calculating Daily file storage allocation for service Id: $workload.", 
        \Zend_Log::INFO);
        try {
            $result = $service->calculateFileStorageUsage($serviceId);
            if ($result) {
                return true;
            } else {
                return false;
            }
        } catch (\exception $e) {
            $logger = \Zend_Registry::getInstance()->get('logger');
            $logger->log($e->getMessage(), \Zend_Log::ERR);
        }
    }
}