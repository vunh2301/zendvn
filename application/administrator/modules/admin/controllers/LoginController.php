<?php

class LoginController extends Zend_Controller_Action
{
    public function indexAction()
    {
    	$db = $this->_getParam('db');
    	$loginForm = new Admin_Form_Login();
    	if($this->_request->isPost()){
	    	if ($loginForm->isValid($_POST)) {
	    		 
	    		$adapter = new Zend_Auth_Adapter_DbTable(
	    				$db,
	    				'users',
	    				'username',
	    				'password',
	    				'MD5(CONCAT(?, password_salt))'
	    		);
	    		 
	    		$adapter->setIdentity($loginForm->getValue('username'));
	    		$adapter->setCredential($loginForm->getValue('password'));
	    		 
	    		$auth   = Zend_Auth::getInstance();
	    		$result = $auth->authenticate($adapter);
	    		 
	    		if ($result->isValid()) {
	    			$auth->getStorage()->write($adapter->getResultRowObject(array(
	    					'id'
	    			)));
	    			$this->_redirect('/administrator');
	    		}
	    	}
    	}
    	$this->view->loginForm = $loginForm;
    }
    public function logoutAction()
    {
    	$auth = Zend_Auth::getInstance();
    	$auth->clearIdentity();
    	$this->_redirect('/administrator');
    }
}