<?php
require_once ('init.php');
$cl = new \Gearman\Client();
$cl->backgroundTask('reverse', "background task successful");
$cl->task('reverse', 'Test Successful');
