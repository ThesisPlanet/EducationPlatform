<?php
// Define configuration path directory (non-replacing for new deployments)
defined('CONFIGURATION_PATH') || define('CONFIGURATION_PATH',
realpath(dirname(__FILE__) . '/../../../../../../configuration'));


defined('APPLICATION_PATH') || define('APPLICATION_PATH',
        realpath(dirname(__FILE__) . '/../../../application'));

// Define application environment
defined('APPLICATION_ENV') ||
         define('APPLICATION_ENV',
                (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
// Ensure library/ is on include_path
set_include_path(
        implode(PATH_SEPARATOR,
                array(
                        realpath(APPLICATION_PATH . '/../library'),
                        get_include_path()
                )));

/**
 * Zend_Application
 */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini');

$application->bootstrap();
$server = new Zend_Json_Server();
$server->setClass('\App\ServiceProxy\User', 'user');
$server->setClass('\App\ServiceProxy\Course', 'course');
$server->setClass('\App\ServiceProxy\Course\Assessment', 'course_assessment');
$server->setClass('\App\ServiceProxy\Question', 'question');
$server->setClass('\App\ServiceProxy\Content\Audio', 'content_audio');
$server->setClass('\App\ServiceProxy\Content\File', 'content_file');
$server->setClass('\App\ServiceProxy\Content\Video', 'content_video');
if ('GET' == $_SERVER['REQUEST_METHOD']) {
    // Indicate the URL endpoint, and the JSON-RPC version used:
    $server->setTarget('/api/v1.0/json.php')->setEnvelope(
            \Zend_Json_Server_Smd::ENV_JSONRPC_2);

    header('Content-Type: application/json');
    echo $server->getServiceMap();
    return;
}
$server->handle();