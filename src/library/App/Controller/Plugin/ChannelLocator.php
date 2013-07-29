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
namespace App\Controller\Plugin;
class ChannelLocator extends \Zend_Controller_Plugin_Abstract
{
    public function preDispatch (\Zend_Controller_Request_Abstract $request)
    {
        // 1. search for a DNS record based on the HTTP_HOST parameter sent in.
        if (\Zend_Registry::isRegistered('channel')) {
            if (is_object(\Zend_Registry::get('channel'))) {}
        } else {
            $dnsService = new \App\Service\Channel();
            $dnsObj = $dnsService->findByDNS($_SERVER['HTTP_HOST']);
            if (is_object($dnsObj)) {
                \Zend_Registry::getInstance()->set('channel', 
                $dnsObj->getChannel());
            } else {
                \Zend_Registry::getInstance()->set('channel', 
                new \App\Entity\Channel());
            }
        }
    }
}