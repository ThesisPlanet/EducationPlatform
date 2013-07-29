<?php
namespace App\Service\Content;

interface iFile extends \App\Service\iContent
{

    public function acl_getThumbnailUrl ($id, $options = array());
}