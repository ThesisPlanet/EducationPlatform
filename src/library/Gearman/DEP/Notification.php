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

class Notification
{

    public function SendCourseWideNotification (\GearmanJob $job)
    {
        $em = \Zend_Registry::get('em');
        $em->getConnection()->close();
        $em->getConnection()->connect();

        $logger = \Zend_Registry::getInstance()->get('logger');

        $workload = unserialize($job->workload());
        $service = new \App\Service\Notification();

        $logger->log(
                "Sending a bulk Message for course: " . $workload['courseId'],
                \Zend_Log::INFO);
        try {

            $templateName = $workload['templateName'];
            $emailSubject = $workload['emailSubject'];
            $courseId = $workload['courseId'];
            $templateParameters = $workload['templateParameters'];
            $result = $service->system_sendBulkNotification($templateName,
                    $emailSubject, $courseId, $templateParameters);
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