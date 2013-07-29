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
$time = microtime(true);
$memory = memory_get_usage();
// Define path to application directory
defined('APPLICATION_PATH') ||
 define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 
(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
// Ensure library/ is on include_path
set_include_path(
implode(PATH_SEPARATOR, 
array(realpath(APPLICATION_PATH . '/../library'), get_include_path())));
/** Zend_Application */
require_once 'Zend/Application.php';
// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, 
APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap();
register_shutdown_function('__shutdown');
function __shutdown ()
{
    global $time, $memory;
    $endTime = microtime(true);
    $endMemory = memory_get_usage();
    echo '
Time [' . ($endTime - $time) .
     '] Memory [' . number_format(($endMemory - $memory) / 1024) . 'Kb]';
}