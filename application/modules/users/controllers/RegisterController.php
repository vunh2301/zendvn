<?php

class Users_registerController extends Zend_Controller_Action
{
	public function indexAction()
    {
    	$this->view->registerForm = new Users_Form_Register();
    }
    
    public function activateAction(){
    	$userId = $this->_request->getParam('uid', 0);
    	$activate = $this->_request->getParam('code', 0);
    	$tblUser = new Library_DbTable_User();
    	$tblUser->find($userId)->current();
    	$user = $tblUser->find($userId)->current();
    	if($user != null){
    		if($user->active == $activate){
    			$user->active = true;
    			$user->save();
    		}
    	}
    	$this->_redirect('/');
    }
    
    function generateRandomString($length = 50) {
    	$strings = '0123456789abcdefghijklmnopqrstuvwxyz';
    	$randomString = '';
    	for ($i = 0; $i < $length; $i++) {
    		$randomString .= $strings[rand(0, strlen($strings) - 1)];
    	}
    	return $randomString;
    }
    
}