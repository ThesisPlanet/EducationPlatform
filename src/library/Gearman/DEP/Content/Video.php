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
namespace Gearman\DEP\Content;

class Video
{

    public function sendOriginalToCloud (\GearmanJob $job)
    {
        $workload = unserialize($job->workload());
        echo "Processing: " . __CLASS__ . __FUNCTION__ . print_r($workload) .
                 "\n";
        $em = \Zend_Registry::get('em');
        $em->getConnection()->close();
        $em->getConnection()->connect();
        $service = new \App\Service\Content\Video();
        try {
            $id = $workload['id'];
            $result = $service->system_sendDataToCloud($workload['id'], 
                    array(
                            'server' => $workload['server']
                    ));
            if (! $result) {} else {
                $service->system_processData($id, $workload);
                $service->system_deleteLocalData($workload['id']);
                // $db->closeConnection();
                return true;
            }
        } catch (\exception $e) {
            $logger = \Zend_Registry::getInstance()->get('logger');
            $logger->log($e->getMessage(), \Zend_Log::ERR);
        }
    }

    public function fetchEncodingProgress (\GearmanJob $job)
    {
        $workload = unserialize($job->workload());
        echo "Processing: " . __CLASS__ . __FUNCTION__ . print_r($workload) .
                 "\n";
        
        $em = \Zend_Registry::get('em');
        $em->getConnection()->close();
        $em->getConnection()->connect();
        $service = new \App\Service\Content\Video();
        try {
            $id = $workload['id'];
            $service->checkEncodingProgress();
            return true;
        } catch (\exception $e) {
            $logger = \Zend_Registry::getInstance()->get('logger');
            $logger->log($e->getMessage(), \Zend_Log::ERR);
        }
    }
}