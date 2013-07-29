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

// Indicate the URL endpoint, and the JSON-RPC version used:
$server->setTarget('/api/v1.0/json.php')->setEnvelope(
        \Zend_Json_Server_Smd::ENV_JSONRPC_2);

$SMD = $server->getServiceMap()->toArray();

// var_dump($SMD);
?>
<html>
<head>
<link rel="stylesheet" href="/css/libs/Twitter/2.0.4/bootstrap.min.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="span12">
				<h1>JSON-RPC Documentation</h1>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Key</th>
							<th>Value</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Envelope</td>
							<td><?php echo $SMD['envelope'];?></td>
						</tr>
						<tr>
							<td>SMD Version</td>
							<td><?php echo $SMD['SMDVersion'];?></td>
						</tr>
						<tr>
							<td>Target URI</td>
							<td><?php echo $SMD['target'];?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="span12">
<?php
foreach ($SMD['services'] as $name => $params) {
    ?>        		<table class="table table-bordered">
					<tr>
						<th class="span6"><?php echo $name;?></th>
						<td><strong>Return: </strong><?php echo $params['returns'];?></td>
					</tr>
					<?php foreach($params['parameters'] as $pname => $p):?>
					<tr>
						<td><?php echo $p['name'];?></td>
						<td><?php echo $p['type'];?></td>
					</tr>
					<?php endforeach;?>
				</table>
<?php }?>
	</div>
		</div>
	</div>
</body>
</html>