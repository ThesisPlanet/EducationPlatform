<?php
require_once ('../bootstrap.php');

$userService = new \App\Service\User();
$userObj = $userService->findByEmail('testuser@thesisplanet.com');

if (is_object($userObj)) {
    \Doctrine\Common\Util\Debug::dump($userObj->getSubscriptions());
}