<?php
class Users_GroupsController extends Zendvn_Controller_Action
{
	public function init(){
		//Zend_Session::namespaceUnset('admin.users.groups');
		$configModule = new Zend_Session_Namespace('admin.users.groups');
		$configGlobal = new Zend_Session_Namespace('admin.global');
		
		$appConfig = Zendvn_Factory::getAppConfig();
		$siteConfig = $appConfig['site'];

		$configModule->filter['search'] 	= $this->_request->getParam('filter_search', $configModule->filter['search'] ? $configModule->filter['search'] : null);
		$configModule->filter['state'] 		= $this->_request->getParam('filter_state', '*') != null ? $this->_request->getParam('filter_state', '*') : $configModule->filter['state'];
		$configModule->filter['order_by'] 	= $this->_request->getParam('order_by', 'ASC') != null ? $this->_request->getParam('order_by', 'ASC') : $configModule->filter['order_by'];
		$configModule->filter['ordering'] 	= $this->_request->getParam('ordering', 'lft') != null ? $this->_request->getParam('ordering', 'lft') : $configModule->filter['ordering'];
		$configModule->filter['paginator'] 	= $this->_request->getParam('paginator', 1) ? $this->_request->getParam('paginator', 1) : $configModule->filter['paginator'];
		$configModule->filter['paginator_per_page'] = $this->_request->getParam('paginator_per_page', $siteConfig['recordPerPage']) != null ? $this->_request->getParam('paginator_per_page', $siteConfig['recordPerPage']) : $configModule->filter['paginator_per_page'];

		$this->view->filter = $configModule->filter;
	}
	
    public function indexAction()
    {   
    	// Other Task
    	$this->taskAction();
    	
    	// Data Table
    	$tblGroup = new Users_Model_DbTable_Group();
    	
    	// Get Items
    	$this->view->paginator = $tblGroup->getItems($this->view->filter);
    	
    	// Assign to View
    	$this->view->groups = $this->view->paginator->getCurrentItems();
    }
    
    public function taskAction(){
    	$task = $this->_request->getParam('task');
    	$tblGroup = new Users_Model_DbTable_Group();
    	if($task == 'create'){
    		$this->_helper->_redirector->gotoSimple('create', 'groups', 'users');
    	}elseif($task == 'edit' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
    		$this->_helper->_redirector->gotoSimple('edit', 'groups', 'users',array('id' => array_shift(array_values($this->view->chekeds))));
    	}elseif($task == 'delete' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
    		$tblUser->deleteItems($this->view->chekeds);
    	}
    }
    
    public function editAction(){
    	$groupId = $this->_request->getParam('id', 0);
    	$task = $this->_request->getParam('task');
    	if($task == 'close') $this->_helper->_redirector->gotoSimple('index', 'groups', 'users');
    	Zendvn_Factory::addBreadcrumb(array(array('label' => 'Edit Group')));
    	
    	// Data Table
    	$tblGroup 	= new Users_Model_DbTable_Group();
    	$form 		= new Users_Form_Group();
    	$group 		= $tblGroup->getItem($groupId);
    	$form->populate($group->toArray());
    	$form->parent_id->setMultiOptions($tblGroup->getParents());
    	if($group->protected == true){
    		$options = $form->parent_id->getMultiOptions();
    		if(isset($options[$groupId])) unset($options[$groupId]);
    		$form->parent_id->setAttrib("disable", array_keys($options));
    	}else{
    		$childs = $tblGroup->getChilds($groupId);
    		$disableOptions = array();
    		if($childs->count() > 0)foreach ($childs as $child)$disableOptions[] = $child->id;
    		$form->parent_id->setAttrib("disable", $disableOptions + array($groupId));
    	}
    	
    	if($this->_request->isPost()){
    		if($form->isValid($this->_request->getPost())){
    			$values = $form->getValues();
    			
    			// Update
    			$tblGroup->updateItem($groupId, $values);
    			
    			// Process Task
    			if($task == 'edit.close'){
    				$this->_helper->_redirector->gotoSimple('index', 'groups', 'users');
    			}elseif($task == 'edit.new'){
    				$this->_helper->_redirector->gotoSimple('create', 'groups', 'users');
    			}else{
    				$this->_helper->_redirector->gotoSimple('edit', 'groups', 'users', array('id' => $groupId));
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
