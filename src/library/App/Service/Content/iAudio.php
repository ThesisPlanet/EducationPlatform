<?php
namespace App\Service\Content;

interface iAudio extends \App\Service\iContent
{

    public function acl_getStreamUrl ($id, $options = array());

    public function getDistributionStreamer ();
}