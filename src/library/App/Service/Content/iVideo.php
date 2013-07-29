<?php
namespace App\Service\Content;

interface iVideo extends \App\Service\iContent
{

    public function acl_getThumbnailUrl ($id, $options = array());

    public function acl_getStreamUrl ($id, $options = array());
}