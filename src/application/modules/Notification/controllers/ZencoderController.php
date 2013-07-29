<?php

class Notification_ZencoderController extends Zend_Controller_Action
{

    public function preDispatch ()
    {
        $this->_helper->layout->disableLayout();
    }

    public function indexAction ()
    {
        $logger = Zend_Registry::getInstance()->get('logger');
        $encoderService = new \App\Service\Queue\Encoder();
        // $videoService = new \App\Service\Content\Video();
        // $audioService = new \App\Service\Content\Audio();
        // Catch notification

        $zencoder = new \Services_Zencoder(
                \Zend_Registry::getInstance()->get('encoder')->zencoder->API_KEY);

        $notification = $zencoder->notifications->parseIncoming();

        if (! is_object($notification)) {
            throw new exception("notification is not an object.");
        }
        if (! isset($notification->job)) {
            throw new exception("No job was provided.");
        }
        if (! isset($notification->job->id)) {
            throw new exception(
                    "No job id was provided. " . print_r($notification, true));
        }
        $matchingJobs = $encoderService->findByJobId($notification->job->id);
        foreach ($matchingJobs as $id => $job) {
            if ($notification->job->state == "finished") {
                $durationInSeconds = $encoderService->fetchDuration(
                        $notification->job->id);
                $sizeInKb = $encoderService->fetchOutputSizeInKb(
                        $notification->job->id);

                $job->setPercentComplete(100);
                $job->setDurationSeconds($durationInSeconds);
                $job->setStatus('finished');
                $job = $encoderService->update($job->toArray());
                if ($job->getObjType() == "video") {
                    $service = new \App\Service\Content\Video();
                    // use a fake admin for doing this
                    $u = new \App\Entity\User();
                    $u->setRole('admin');
                    $u->setEmail('zencoder@thesisplanet.com');
                    $u->setFirstname('Zencoder Notification');
                    $u->setActivated(true);
                    $u->setLastname('controller');

                    $service->setUser($u);
                } elseif ($job->getObjType() == "audio") {
                    $service = new \App\Service\Content\Audio();
                    $u = new \App\Entity\User();
                    $u->setRole('admin');
                    $u->setEmail('zencoder@thesisplanet.com');
                    $u->setFirstname('Zencoder Notification');
                    $u->setLastname('controller');
                    $u->setActivated(true);

                    $service->setUser($u);
                } else {
                    throw new \exception(
                            "Notification/ZencoderController - No type specified.");
                }
                $obj = $service->find($job->getObjId());
                if (! is_object($obj)) {
                    throw new exception("no object found.");
                } else {

                    $form = $service->getForm();
                    $form->removeElement('channel');
                    $form->removeElement('originalExtension');
                    $form->removeElement('originalSizeKB');
                    $form->removeElement('convertedSizeKB');
                    $form->removeElement('file');
                    $obj = $service->system_updateStatus($job->getObjId(),
                            "ready", $sizeInKb, $durationInSeconds, null);
                }
            } else {
                $obj = $service->find($notification->job->getObjId());
                $obj = $service->system_updateStatus(
                        $notification->job->getObjId(), $notification->state, 0,
                        null);
                $logger->log(
                        "Notification/ZencoderController error: There was a problem encountered with encoding job (Zencoder ID: " .
                                 $notification->job->id . ") - State: " .
                                 $notification->state, Zend_Log::ERR);
            }
        }
    }
}

