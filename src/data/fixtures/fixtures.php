<?php

/**
 * USERS
 */
$visitor = new \App\Entity\User();
$visitor->setEmail('testvisitor@thesisplanet.com');
$visitor->setUsername('testvisitor');
$visitor->setPassword('testvisitor');
$visitor->setFirstname('Visitor');
$visitor->setLastname('Account');
$visitor->setRole('visitor');
$visitor->setActivated(true);
$em->persist($visitor);

$user = new \App\Entity\User();
$user->setEmail('testuser@thesisplanet.com');
$user->setUsername('testuser');
$user->setPassword('testuser');
$user->setFirstname('user');
$user->setLastname('Account');
$user->setRole('user');
$user->setActivated(true);
$em->persist($user);

$subscriber = new \App\Entity\User();
$subscriber->setEmail('testsubscriber@thesisplanet.com');
$subscriber->setUsername('testsubscriber');
$subscriber->setPassword('testsubscriber');
$subscriber->setFirstname('subscriber');
$subscriber->setLastname('Account');
$subscriber->setRole('subscriber');
$subscriber->setActivated(true);
$em->persist($subscriber);

$provider = new \App\Entity\User();
$provider->setEmail('testprovider@thesisplanet.com');
$provider->setUsername('testprovider');
$provider->setPassword('testprovider');
$provider->setFirstname('provider');
$provider->setLastname('Account');
$provider->setRole('provider');
$provider->setActivated(true);
$em->persist($provider);

$admin = new \App\Entity\User();
$admin->setEmail('testadmin@thesisplanet.com');
$admin->setUsername('testadmin');
$admin->setPassword('testadmin');
$admin->setFirstname('admin');
$admin->setLastname('Account');
$admin->setRole('admin');
$admin->setActivated(true);
$em->persist($admin);

$em->flush();
echo "User fixtures created.\n";


/**
 * SAMPLE COURSE
 */

$course = new \App\Entity\Course();
$course->setDescription("Course Fixture description");
$course->setIsApprovalRequired(false);
$course->setIsEnabled(true);
$course->setIsPublished(true);
$course->setIsSearchable(true);
$course->setPrice("0.00");
$course->setTitle("Sample course generated by Fixtures");
$course->setTopic("Courses created by data fixtures");
$em->persist($course);
$em->flush();


/**
 * CONTENT PIECES FOR ACL
 */

// Content ID 1
$a = new \App\Entity\Content\Audio();
//$a->setChapter("Sample Chapter");
$a->setCleanedAt(null);
$a->setConvertedSizeKB(4096);
$a->setCourse($course);
$a->setDeletedAt(null);
$a->setDescription("Audio created by data fixture");
$a->setIsEnabled(true);
$a->setIsPublished(true);
$a->setOriginalExtension("wav");
$a->setOriginalSizeKB("4096");
$a->setRole("subscriber");
$a->setStatus("ready");
$a->setTitle("This is a sample audio fixture");
$em->persist($a);
$em->flush();

// Content ID 2
$f = new \App\Entity\Content\File();
//$f->setChapter("Sample Chapter");
$f->setCleanedAt(null);
$f->setCourse($course);
$f->setDeletedAt(null);
$f->setDescription("Audio created by data fixture");
$f->setIsEnabled(true);
$f->setIsPublished(true);
$f->setOriginalExtension("wav");
$f->setOriginalSizeKB("4096");
$f->setRole("subscriber");
$f->setStatus("ready");
$f->setTitle("This is a sample audio fixture");
$em->persist($f);
$em->flush();
// Content ID 3

$v = new \App\Entity\Content\Video();
//$v->setChapter("Sample Chapter");
$v->setCleanedAt(null);
$v->setConvertedSizeKB(4096);
$v->setCourse($course);
$v->setDeletedAt(null);
$v->setDescription("Audio created by data fixture");
$v->setIsEnabled(true);
$v->setIsPublished(true);
$v->setOriginalExtension("wav");
$v->setOriginalSizeKB("4096");
$v->setRole("subscriber");
$v->setStatus("ready");
$v->setTitle("This is a sample audio fixture");
$em->persist($v);
$em->flush();