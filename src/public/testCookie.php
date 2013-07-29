<?php
setcookie ( 'form-123', base64_encode ( $_SERVER ['HTTP_REFERER'] ), time () + 3600 );
echo $_COOKIE ['form-123'];
$this->_helper->viewRenderer->setNoRender ( true );
