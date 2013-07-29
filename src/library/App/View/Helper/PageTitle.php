<?php
class Zend_View_Helper_PageTitle extends Zend_View_Helper_Abstract {
	public function pageTitle($title) {
		$this->view->headTitle($title);
		$this->view->pageTitle = '<h1>' . $title . '</h1>';
	}
}
