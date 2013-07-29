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
namespace App\Service\Core;

class DistributedStorage extends \App\Service\Base
{

    public function fetch ($server, $type, $filename)
    {
        if (file_exists(SHARE_PATH . "/$type/$filename")) {
            echo "File already exists.";
            return true;
        } else {
            $protocol = \Zend_Registry::getInstance()->get(
                    'internalCommunication')->PROTOCOL;
            
            if (empty($protocol)) {
                $protocol = "http";
            }
            
            $port = \Zend_Registry::getInstance()->get('internalCommunication')->PORT;
            if (empty($port)) {
                $port = "82";
            }
            $address = "$protocol://$server:$port/$type/$filename";
            echo "Requesting $address\n";
            $ch = curl_init("$protocol://$server:$port/$type/$filename");
            $fp = fopen(SHARE_PATH . "/$type/$filename", 'w+');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
            $stat = fstat($fp);
            $size = $stat['size'];
            fclose($fp);
            if ($size == 0) {
                echo "No file was provided at $address.\n";
                return false;
            }
            if ($result) {
                return true;
            } else {
                echo "There was a problem downloading the file.\n";
                return false;
            }
        }
    }

    public function delete ($server, $type, $filename)
    {
        // Delete the file locally as well.
        if (file_exists(SHARE_PATH . "/$type/$filename")) {
            unlink(SHARE_PATH . "/$type/$filename");
            return true;
        }
        // TODO: Figure out the communication going back to the server.
    }
}