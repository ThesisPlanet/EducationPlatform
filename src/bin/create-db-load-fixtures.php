<?php
require_once(realpath(__DIR__) . '/bootstrap.php');
echo exec("php " . realpath(__DIR__) ."/doctrine.php orm:schema-tool:drop --force") . PHP_EOL;
echo exec("php " . realpath(__DIR__) ."/doctrine.php orm:schema-tool:create") . PHP_EOL;
$container = $application->getBootstrap()->getResource('doctrine');
$em = $container->getEntityManager();
echo "Database fixtures loaded successfully!" . PHP_EOL;
require_once( realpath(APPLICATION_PATH . '/../data/fixtures/') . '/fixtures.php');