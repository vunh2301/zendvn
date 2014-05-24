<?php

class Users_logoutController extends Zend_Controller_Action
{
	public function indexAction()
    {
    	$auth = Zend_Auth::getInstance();
    	$auth->clearIdentity();
     //$this->_forward('index', 'index', 'index');
				$auth->getStorage()->read()->role = 'guest';
    	$this->_redirect('/');
    }
}