#!/usr/bin/php
<?php
require_once('../library/Gearman/init.php');
fwrite(STDOUT,"Please specify the function to execute.\n");
$function = rtrim(fgets(STDIN));
fwrite(STDOUT,"received $function.\n");
fwrite(STDOUT,"Please provide the serialized parameters.\n");
$workload = unserialize(rtrim(fgets(STDIN)));
fwrite(STDOUT,"Received " . print_r(unserialize($workload),true) . "as the workload.\n");
$cl = new \Gearman\Client();
fwrite(STDOUT,print_r($cl->backgroundTask($function,$workload),true));
