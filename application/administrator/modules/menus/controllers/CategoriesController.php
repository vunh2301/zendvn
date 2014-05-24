<?php

class Menus_CategoriesController extends Zend_Controller_Action
{    
    public function init(){
    	$configModule = new Zend_Session_Namespace('admin.menus.menus');
    	$configGlobal = new Zend_Session_Namespace('admin.global');
    
    	$appConfig = Zendvn_Factory::getAppConfig();
    	$siteConfig = $appConfig['site'];
    
    	$configModule->filter['search'] 	= $this->_request->getParam('filter_search', $configModule->filter['search'] ? $configModule->filter['search'] : null);
    	$configModule->filter['order_by'] 	= $this->_request->getParam('order_by', 'ASC') != null ? $this->_request->getParam('order_by', 'ASC') : $configModule->filter['order_by'];
    	$configModule->filter['ordering'] 	= $this->_request->getParam('ordering', 'title') != null ? $this->_request->getParam('ordering', 'title') : $configModule->filter['ordering'];
    	$configModule->filter['paginator'] 	= $this->_request->getParam('paginator', 1) ? $this->_request->getParam('paginator', 1) : $configModule->filter['paginator'];
    	$configModule->filter['paginator_per_page'] = $this->_request->getParam('paginator_per_page', $siteConfig['recordPerPage']) != null ? $this->_request->getParam('paginator_per_page', $siteConfig['recordPerPage']) : $configModule->filter['paginator_per_page'];
    
    	$this->view->filter = $configModule->filter;
    }
	
    public function indexAction()
    {
    	
    	// Other Task
    	$this->taskAction();
    	
    	// Data table
    	$tblCatagory = new Menus_Model_DbTable_Category();
    	
    	// Get Paginator
		$this->view->paginator = $tblCatagory->getItems($this->view->filter);
		
		// Assign to View
		$this->view->menus = $this->view->paginator->getCurrentItems();

    }
    
    public function taskAction(){
    	$task = $this->_request->getParam('task');
    	$tblCatagory = new Menus_Model_DbTable_Category();
    	if($task == 'create'){
    		$this->_helper->_redirector->gotoSimple('create', 'categories', 'menus');
    	}elseif($task == 'edit' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
    		$this->_helper->_redirector->gotoSimple('edit', 'categories', 'menus',array('id' => array_shift(array_values($this->view->chekeds))));
    	}elseif($task == 'delete' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
    		$tblCatagory->deleteItems($this->view->chekeds);
    	}
    }
   
    public function createAction(){
    	$task = $this->_request->getParam('task');
    	if($task == 'close') $this->_helper->_redirector->gotoSimple('index', 'categories', 'menus');
    	Zendvn_Factory::addBreadcrumb(array(array('label' => 'New Menu')));
    	
    	$tblMenu 	= new Menus_Model_DbTable_Category();
    	$form 			= new Menus_Form_Menu();
    	
    	if($this->_request->isPost()){
    		if($form->isValid($this->_request->getPost())){
    			$values = $form->getValues();
    			 
    			// Update
    			$menuId = $tblMenu->createItem($values);
    			 
    			// Process Task
    			if($task == 'edit.close'){
    				$this->_helper->_redirector->gotoSimple('index', 'categories', 'menus');
    			}elseif($task == 'edit.new'){
    				$this->_helper->_redirector->gotoSimple('create', 'categories', 'menus');
    			}elseif($task == 'edit.copy'){
    				$menuId = $tblMenu->copyItem($menuId);
    				$this->_helper->_redirector->gotoSimple('edit', 'categories', 'menus', array('id' => $menuId));
    			}else{
    				$this->_helper->_redirector->gotoSimple('edit', 'categories', 'menus', array('id' => $menuId));
    			}
    		}
    	}
    	$this->view->form = new Menus_Form_Menu();
    }
    
    public function editAction(){
    	$menuId = $this->_request->getParam('id', 0);
    	$task = $this->_request->getParam('task');
    	if($task == 'close') $this->_helper->_redirector->gotoSimple('index', 'categories', 'menus');
    	Zendvn_Factory::addBreadcrumb(array(array('label' => 'Edit Menu')));
    	
    	$tblMenu 	= new Menus_Model_DbTable_Category();
    	$menu 			= $tblMenu->getItem($menuId);
    	$form 			= new Menus_Form_Menu();
    	
    	$form->populate($menu->toArray());
    	
    	if($this->_request->isPost()){
    		if($form->isValid($this->_request->getPost())){
    			$values = $form->getValues();
    			
    			// Update
    			$tblMenu->updateItem($menuId, $values);
    			
    			// Process Task
    			if($task == 'edit.close'){
    				$this->_helper->_redirector->gotoSimple('index', 'categories', 'menus');
    			}elseif($task == 'edit.new'){
    				$this->_helper->_redirector->gotoSimple('create', 'categories', 'menus');
    			}elseif($task == 'edit.copy'){
    				$menuId = $tblMenu->copyItem($menuId);
    				$this->_helper->_redirector->gotoSimple('edit', 'categories', 'menus', array('id' => $menuId));
    			}else{
    				$this->_helper->_redirector->gotoSimple('edit', 'categories', 'menus', array('id' => $menuId));
    			}
    		}
    	}
    	 
    	$this->view->form = $form;
    }
}
