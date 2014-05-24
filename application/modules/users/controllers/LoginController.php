<?php

class Users_loginController extends Zend_Controller_Action
{
	public function indexAction()
    {
    	 Zend_Debug::dump(
    	 		$this->view->route(
    	 				array(
    	 						'module' => 'contents',
    	 						'controller' => 'category',
    	 						'action' => 'index',
    	 						'cid' => 6
    	 				)
    	 		)
    	 );
    	 Zend_Debug::dump(
    	 		$this->view->route(
    	 				array(
    	 						'module' => 'users',
    	 						'controller' => 'login',
    	 						'action' => 'index'
    	 				)
    	 		)
    	 );
    	$db = $this->_getParam('db');
    	$loginForm = new Users_Form_Login();
    	 
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
    					'id',
    					'username',
    					'real_name',
    					'role'
    			)));
    			$this->_redirect('/');
    			//$this->_redirect($this->getRequest()->getRequestUri());
    			
    		}
    	}
    	$this->view->loginForm = $loginForm;
    }
}