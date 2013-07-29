<?php
require_once (realpath(__DIR__) . '/../bootstrap.php');
$container = $application->getBootstrap()->getResource('doctrine');
$em = $container->getEntityManager();
echo "Database fixtures loaded successfully!" . PHP_EOL;
require_once (realpath(APPLICATION_PATH . '/../data/fixtures/') .
         '/singleAdmin.php');