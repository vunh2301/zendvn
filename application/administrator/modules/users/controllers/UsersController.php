<?php
class Users_UsersController extends Zendvn_Controller_Action
{

	public function init(){
		//Zend_Session::namespaceUnset('admin.users.users');
		$configModule = new Zend_Session_Namespace('admin.users.users');
		$configGlobal = new Zend_Session_Namespace('admin.global');
		
		$appConfig = Zendvn_Factory::getAppConfig();
		$siteConfig = $appConfig['site'];

		$configModule->filter['search'] 	= $this->_request->getParam('filter_search', $configModule->filter['search'] ? $configModule->filter['search'] : null);
		$configModule->filter['state'] 		= $this->_request->getParam('filter_state', '*') != null ? $this->_request->getParam('filter_state', '*') : $configModule->filter['state'];
		$configModule->filter['status'] 	= $this->_request->getParam('filter_status', '*') != null ? $this->_request->getParam('filter_status', '*') : $configModule->filter['status'];
		$configModule->filter['group'] 		= $this->_request->getParam('filter_group', '*') != null ? $this->_request->getParam('filter_group', '*') : $configModule->filter['group'];
		$configModule->filter['order_by'] 	= $this->_request->getParam('order_by', 'ASC') != null ? $this->_request->getParam('order_by', 'DESC') : $configModule->filter['order_by'];
		$configModule->filter['ordering'] 	= $this->_request->getParam('ordering', 'title') != null ? $this->_request->getParam('ordering', 'real_name') : $configModule->filter['ordering'];
		$configModule->filter['paginator'] 	= $this->_request->getParam('paginator', 1) ? $this->_request->getParam('paginator', 1) : $configModule->filter['paginator'];
		$configModule->filter['paginator_per_page'] = $this->_request->getParam('paginator_per_page', $siteConfig['recordPerPage']) != null ? $this->_request->getParam('paginator_per_page', $siteConfig['recordPerPage']) : $configModule->filter['paginator_per_page'];

		$this->view->filter = $configModule->filter;
	}
	
    public function indexAction()
    {   
    	// Other Task
    	$this->taskAction();
    	
    	// Data Table
    	$tblUser = new Users_Model_DbTable_User();
    	
    	// Get Items
    	$this->view->paginator = $tblUser->getItems($this->view->filter);
    	
    	// Assign to View
    	$this->view->users = $this->view->paginator->getCurrentItems();
    	$this->view->select_groups = $tblUser->getGroups();
    }
    
    public function taskAction(){
    	$task = $this->_request->getParam('task');
    	$tblUser = new Users_Model_DbTable_User();
    	if($task == 'create'){
    		$this->_helper->_redirector->gotoSimple('create', 'users', 'users');
    	}elseif($task == 'edit' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
    		$this->_helper->_redirector->gotoSimple('edit', 'users', 'users',array('id' => array_shift(array_values($this->view->chekeds))));
    	}elseif(($task == 'enable' || $task == 'disable') && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
    		$tblUser->updateState($this->view->chekeds, ($task == 'enable'));
    	}elseif($task == 'activate' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
    		$tblUser->updateActivate($this->view->chekeds);
    	}elseif($task == 'delete' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
    		foreach ($this->view->chekeds as $recordId){
    			$tblUser->deleteItem($recordId);
    		}
    	}
    }
    
    public function editAction(){
    	$userId = $this->_request->getParam('id', 0);
    	$task = $this->_request->getParam('task');
    	if($task == 'close') $this->_helper->_redirector->gotoSimple('index', 'users', 'users');
    	Zendvn_Factory::addBreadcrumb(array(array('label' => 'Edit User')));
    	
    	// Data Table
    	$tblUsers 	= new Users_Model_DbTable_User();
    	$form 		= new Users_Form_Profile();
    	$user 		= $tblUsers->getItem($userId);
    	$form->populate($user->toArray());
    	
    	$form->template->setMultiOptions(array(0 => 'User Default') + (array)$tblUsers->getTemplates());
    	
    	$form->groups->setMultiOptions($tblUsers->getGroups());
    	// Update value
    	$groups = $user->findManyToManyRowset('Users_Model_DbTable_Group', 'Users_Model_DbTable_UserGroup');
    	if($groups->count() > 0){
    		foreach ($groups as $group){
    			$checkedValue[] = $group->id;
    		}
    		$form->groups->setValue($checkedValue);
    	}
    	
    	//Add Validate
    	$form->username->addValidator(new Zend_Validate_Alnum(array('allowWhiteSpace' => false)));
    	$form->username->addValidator(new Zend_Validate_StringLength(array('min' => 4, 'max' => 16)));
    	$form->username->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'username', 'exclude' => array(
    			'field' => 'id',
    			'value' => $userId
    	))));
    	 
    	$form->password_confirm->addValidator('Identical', false, array('token' => 'password'));
    	$form->password_confirm->addErrorMessage('The passwords do not match');
    	 
    	$form->email->addValidator('EmailAddress');
    	$form->email->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'email', 'exclude' => array(
    			'field' => 'id',
    			'value' => $userId
    	))));
    	
    	if($this->_request->isPost()){
    		if($form->isValid($this->_request->getPost())){
    			$values = array_shift(array_values($form->getValues()));
    			
    			// Update
    			$tblUsers->updateItem($userId, $values);
    			
    			// Process Task
    			if($task == 'edit.close'){
    				$this->_helper->_redirector->gotoSimple('index', 'users', 'users');
    			}elseif($task == 'edit.new'){
    				$this->_helper->_redirector->gotoSimple('create', 'users', 'users');
    			}else{
    				$this->_helper->_redirector->gotoSimple('edit', 'users', 'users', array('id' => $userId));
    			}
    		}
    	}
    	
    	$this->view->form = $form;
    }
    
    public function createAction(){
    	$userId = $this->_request->getParam('id', 0);
    	$task = $this->_request->getParam('task');
    	if($task == 'close') $this->_helper->_redirector->gotoSimple('index', 'users', 'users');
    	Zendvn_Factory::addBreadcrumb(array(array('label' => 'New User')));
    	 
    	// Data Table
    	$tblUsers 	= new Users_Model_DbTable_User();
    	$form 		= new Users_Form_Profile();
    	 
    	$form->template->setMultiOptions(array(0 => 'User Default') + (array)$tblUsers->getTemplates());
    	 
    	$form->groups->setMultiOptions($tblUsers->getGroups());
    	$form->groups->setValue(2);
    	 
    	//Add Validate
    	$form->username->addValidator(new Zend_Validate_Alnum(array('allowWhiteSpace' => false)));
    	$form->username->addValidator(new Zend_Validate_StringLength(array('min' => 4, 'max' => 16)));
    	$form->username->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'username')));
    
    	$form->password->setRequired(true);
    	$form->password->addValidator(new Zend_Validate_StringLength(array('min' => 6, 'max' => 16)));
    	
    	$form->password_confirm->addValidator('Identical', false, array('token' => 'password'));
    	$form->password_confirm->addErrorMessage('The passwords do not match');
    
    	$form->email->addValidator('EmailAddress');
    	$form->email->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'email')));
    	
    	//set default timezone
    	$appConfig = Zendvn_Factory::getAppConfig();
    	$siteConfig = $appConfig['site'];
    	$form->timezone->setValue($siteConfig['timezone']);

    	 
    	if($this->_request->isPost()){
    		if($form->isValid($this->_request->getPost())){
    			$values = array_shift(array_values($form->getValues()));
    			 
    			// Update
    			$userId = $tblUsers->createItem($values);
    			 
    			// Process Task
    			if($task == 'edit.close'){
    				$this->_helper->_redirector->gotoSimple('index', 'users', 'users');
    			}elseif($task == 'edit.new'){
    				$this->_helper->_redirector->gotoSimple('create', 'users', 'users');
    			}else{
    				$this->_helper->_redirector->gotoSimple('edit', 'users', 'users', array('id' => $userId));
    			}
    		}
    	}
    	 
    	$this->view->form = $form;
    }

}
