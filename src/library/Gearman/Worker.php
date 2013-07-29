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
require_once ('init.php');
ini_set('memory_limit', - 1);
$worker = new \GearmanWorker();

$queue = \Zend_Registry::getInstance()->get('queue');
$config = $queue->getConfig();
foreach ($config->Gearman->server as $key => $value) {
    $worker->addServer($value);
}
/**
 * Audio Functions
 */
$audio = new \Gearman\DEP\Content\Audio();
$worker->addFunction('DEP_Audio_SendOriginalToCloud',
        array(
                $audio,
                'SendOriginalToCloud'
        ));
/**
 * File Functions
 */
$file = new \Gearman\DEP\Content\File();
$worker->addFunction('DEP_File_SendOriginalToCloud',
        array(
                $file,
                'sendOriginalToCloud'
        ));
/**
 * Video Functions
 */
$video = new \Gearman\DEP\Content\Video();
$worker->addFunction('DEP_Video_SendOriginalToCloud',
        array(
                $video,
                'sendOriginalToCloud'
        ));
/**
 * Billing functions
 */
// Storage calculation
$billing = new \Gearman\DEP\Billing();
$worker->addFunction('DEP_Billing_CalculateAudioStorageUsage',
        array(
                $billing,
                'CalculateAudioStorageUsage'
        ));
$worker->addFunction('DEP_Billing_CalculateFileStorageUsage',
        array(
                $billing,
                'CalculateFileStorageUsage'
        ));
$worker->addFunction('DEP_Billing_CalculateVideoStorageUsage',
        array(
                $billing,
                'CalculateVideoStorageUsage'
        ));
// Utilization aggregation
$utilizationAggregateMonthly = new \Gearman\DEP\Utilization\AggregateMonthly();
$worker->addFunction('DEP_Utilization_Aggregate_calculateStorage',
        array(
                $utilizationAggregateMonthly,
                'calculateStorage'
        ));
$worker->addFunction('DEP_Utilization_Aggregate_allocateDownload',
        array(
                $utilizationAggregateMonthly,
                'allocateDownload'
        ));
$worker->addFunction('DEP_Utilization_Aggregate_allocateStreaming',
        array(
                $utilizationAggregateMonthly,
                'allocateStreaming'
        ));
$worker->addFunction('DEP_Utilization_Aggregate_allocateEncodingTime',
        array(
                $utilizationAggregateMonthly,
                'allocateEncodingTime'
        ));

// Notification Service

$NotificationService = new \Gearman\DEP\Notification();
$worker->addFunction('DEP_Notification_SendCourseWideNotification',
        array(
                $NotificationService,
                'SendCourseWideNotification'
        ));

echo "Starting gearman worker...\n";
//
//
// DO NOT ADD ANYTHING BELOW THIS LINE.
//
//
/**
 * Execution
 */
$lastConnectionTime = new \Zend_Date();
try {
    while ($worker->work()) {
        if (GEARMAN_SUCCESS != $worker->returnCode()) {
            echo "Worker failed: " . $worker->error() . "\n";
        }
    }
} catch (\exception $e) {
    $logger = \Zend_Registry::get('logger');
    $logger->log(
            'Gearman Worker Exception (unable to perform work): ' .
                     $e->getMessage(), \Zend_Log::CRIT);
    die();
}

