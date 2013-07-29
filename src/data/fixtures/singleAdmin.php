<?php

// Check for the number of Admins

$repository = $em->getRepository('\App\Entity\User');

$adminList = $repository->findBy(array(
        'role' => "admin"
));

if (count($adminList) == 0) {

    $admin = new \App\Entity\User();
    $admin->setEmail('custadmin@thesisplanet.com');
    $admin->setUsername('tempAdmin');
    $admin->setPassword('custadmin');
    $admin->setFirstname('Temporary Administration');
    $admin->setLastname('Account');
    $admin->setRole('admin');
    $admin->setActivated(true);
    $em->persist($admin);
    $em->flush();
    echo "Created custadmin@thesisplanet.com as administrator because no admin accounts were found.\n";
}