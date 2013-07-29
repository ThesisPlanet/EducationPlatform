<?php
require_once ('../bootstrap.php');
$s = new \App\Service\Channel();
echo \Doctrine\Common\Util\Debug::dump($s->findPrimaryDNS(1));