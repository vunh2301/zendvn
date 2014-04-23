<?php
class Contents_CategoriesController extends Zend_Controller_Action
{
	public function init(){
		$configModule = new Zend_Session_Namespace('admin.contents.categories');
		$configGlobal = new Zend_Session_Namespace('admin.global');
		
		$appConfig = Zendvn_Factory::getAppConfig();
		$siteConfig = $appConfig['site'];
		
		$configModule->filter['search'] 	= $this->_request->getParam('filter_search', $configModule->filter['search'] ? $configModule->filter['search'] : null);
		$configModule->filter['level'] 		= $this->_request->getParam('filter_level', '*') != null ? $this->_request->getParam('filter_level', '*') : $configModule->filter['level'];
		$configModule->filter['status'] 	= $this->_request->getParam('filter_status', '*') != null ? $this->_request->getParam('filter_status', '*') : $configModule->filter['status'];
		$configModule->filter['access'] 	= $this->_request->getParam('filter_access', '*') != null ? $this->_request->getParam('filter_access', '*') : $configModule->filter['access'];
		$configModule->filter['order_by'] 	= $this->_request->getParam('order_by', 'ASC') != null ? $this->_request->getParam('order_by', 'ASC') : $configModule->filter['order_by'];
		$configModule->filter['ordering'] 	= $this->_request->getParam('ordering', 'title') != null ? $this->_request->getParam('ordering', 'lft') : $configModule->filter['ordering'];
		$configModule->filter['paginator'] 	= $this->_request->getParam('paginator', 1) ? $this->_request->getParam('paginator', 1) : $configModule->filter['paginator'];
		$configModule->filter['paginator_per_page'] = $this->_request->getParam('paginator_per_page', $siteConfig['recordPerPage']) != null ? $this->_request->getParam('paginator_per_page', $siteConfig['recordPerPage']) : $configModule->filter['paginator_per_page'];
		
		$this->view->filter = $configModule->filter;
		
	}
	
	public function indexAction(){
		$this->view->headScript()->appendFile($this->view->baseUrl("/templates/admin/js/holder.js"));
		
		// Process Task
		$this->taskAction();
		
		// Data Table
		$tblCategory = new Contents_Model_DbTable_Category();	

		// Get Paginator
		$this->view->paginator = $tblCategory->getItems($this->view->filter);
		
		// Assign to View
		$this->view->categories = $this->view->paginator->getCurrentItems();
		
	}
	
	public function taskAction(){
		$task = $this->_request->getParam('task');
		$tblCategory = new Contents_Model_DbTable_Category();
		if($task == 'create'){
			$this->_helper->_redirector->gotoSimple('create', 'categories', 'contents');
		}elseif($task == 'edit' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
			$this->_helper->_redirector->gotoSimple('edit', 'categories', 'contents',array('id' => array_shift(array_values($this->view->chekeds))));
		}elseif(($task == 'publish' || $task == 'unpublish' || $task == 'trash') && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
			$tblCategory->updateStatus($this->view->chekeds, $task);
		}elseif($task == 'order' && ($orderValues = $this->_request->getParam('pre_order', null)) !== null){	
			$orderValues = json_decode($orderValues, true);
			$parentId = null;
			foreach ($orderValues as $orderValue){
				if(null === $parentId){
					$parentId = $tblCategory->getNode($orderValue['id'])->parent_id;
				}
				$tblCategory->moveNode($orderValue['id'], 'right', $parentId);
			}
		}elseif($task == 'delete' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
			foreach ($this->view->chekeds as $recordId){
				$tblCategory->deleteItem($recordId);
			}
		}
	}
	
	public function editAction(){
		$categoryId = $this->_request->getParam('id', 0);
		$task 		= $this->_request->getParam('task');
		if($task == 'close') $this->_helper->_redirector->gotoSimple('index', 'categories', 'contents');
		Zendvn_Factory::addBreadcrumb(array(array('label' => 'Edit Category')));
		
		// Data Table
		$tblResource 	= new Zendvn_Db_Table_AclResource();
		$tblCategory 	= new Contents_Model_DbTable_Category();
		$form 			= new Contents_Form_Category();
		
		$category = $tblCategory->getItem($categoryId);
		$form->populate($category->toArray());
		
		if($category->image)$form->image->setAttrib('src', $this->view->baseUrl('/modules/contents/images/thumbnails/' . $category->image));
		
		$parentId = $this->_request->getPost('parent_id', $category->parent_id);
		
		$form->parent_id->setMultiOptions($tblCategory->getParents($categoryId));
		$form->parent_id->setAttrib('onchange', '$.post(\'' . $this->view->url(array('module' => 'contents', 'controller' => 'categories', 'action' => 'ajax'), null, true) . '/task/order/id/' . $categoryId . '/pid/\' + $(\'#parent_id\').val(), function(data){$(\'.dd\').html(data).nestable({maxDepth: 1}); $(\'#order\').val($(\'.dd .active\').index() > 0 ? $(\'.dd .active\').index() : 0);});');
		
		$this->view->order_category = $tblCategory->getOrderModal($categoryId, $parentId);
		
		//Add Alias Validate
		$validateAlias = new Zend_Validate_Db_NoRecordExists(array('table' => 'categories','field' => 'alias', 'exclude' => array(
				'field' => 'id',
				'value' => $categoryId
		)));
		$validateAlias->setSelect($validateAlias->getSelect()->where('parent_id = ?', $parentId));
		$form->alias->addValidator($validateAlias);
		
		if($this->_request->isPost()){
			// Check alias after check validate
			$post = $this->_request->getPost();
			if($post['alias'] == null) $post['alias'] = $post['title'];
			$post['alias'] = $tblCategory->createAlias($post['alias']);
			// Check validate
			if($form->isValid($post)){
				$values = array_shift(array_values($form->getValues()));
				// Upload Image
				if ($form->image->isUploaded()) {
					$imagePath = PUBLISH_PATH . '/modules/contents/images/';
					if(is_file($imagePath . $category->image))unlink($imagePath . $category->image);
					if(is_file($imagePath . 'thumbnails/' .  $category->image))unlink($imagePath . 'thumbnails/' .  $category->image);
					$values['image'] = $tblCategory->updateImage($values['image']);
				}else{
					unset($data['image']);
				}

				// Update
				$tblCategory->updateItem($categoryId, $values);
				
				//Update Permission
				if($values['parent_id'] != $category->parent_id){
					if($values['parent_id'] > 1){
						$tblResource->moveResource('contents.categories.' . $categoryId, 'contents.categories.' . $parentId);
					}else{
						$tblResource->moveResource('contents.categories.' . $categoryId, 'contents');
					}
				}
				$tblResource->updatePrivileges('contents.categories.' . $categoryId, (array)$this->_request->getParam('contentsCategories'));

				// Process Task
				if($task == 'edit.close'){
					$this->_helper->_redirector->gotoSimple('index', 'categories', 'contents');
				}elseif($task == 'edit.new'){
					$this->_helper->_redirector->gotoSimple('create', 'categories', 'contents');
				}elseif($task == 'edit.copy'){
					
				}else{
					$this->_helper->_redirector->gotoSimple('edit', 'articles', 'categories', array('id' => $categoryId));
				}
			}
		}
		
		$this->view->form = $form;
		$this->view->permission = Zendvn_Factory::getAcl()->getForm('contents.categories.' . $categoryId, 'contents.categories', 'contentsCategories');
	}
	
	public function createAction(){
		$task 		= $this->_request->getParam('task');
		if($task == 'close') $this->_helper->_redirector->gotoSimple('index', 'categories', 'contents');
		Zendvn_Factory::addBreadcrumb(array(array('label' => 'New Category')));
	
		// Data Table
		$tblResource 	= new Zendvn_Db_Table_AclResource();
		$tblCategory 	= new Contents_Model_DbTable_Category();
		$form 			= new Contents_Form_Category();

		$parentId = $this->_request->getPost('parent_id', 1);
		
		$form->parent_id->setMultiOptions($tblCategory->getParents());
		$form->parent_id->setAttrib('onchange', '$.post(\'' . $this->view->url(array('module' => 'contents', 'controller' => 'categories', 'action' => 'ajax'), null, true) . '/task/order/id/0/pid/\' + $(\'#parent_id\').val(), function(data){$(\'.dd\').html(data).nestable({maxDepth: 1}); $(\'#order\').val($(\'.dd .active\').index() > 0 ? $(\'.dd .active\').index() : 0);});');
		
		$this->view->order_category = $tblCategory->getOrderModal(0, $parentId);
		
		//Add Alias Validate
		$validateAlias = new Zend_Validate_Db_NoRecordExists(array('table' => 'categories','field' => 'alias'));
		$validateAlias->setSelect($validateAlias->getSelect()->where('parent_id = ?', $parentId));
		$form->alias->addValidator($validateAlias);
		
		if($this->_request->isPost()){
			// Check alias after check validate
			$post = $this->_request->getPost();
			if($post['alias'] == null) $post['alias'] = $post['title'];
			$post['alias'] = $tblCategory->createAlias($post['alias']);
			// Check validate
			if($form->isValid($post)){
				$values = array_shift(array_values($form->getValues()));
				
				// Upload Image
				if ($form->image->isUploaded()) {
					$imagePath = PUBLISH_PATH . '/modules/contents/images/';
					$values['image'] = $tblCategory->updateImage($values['image']);
				}
				$parentId = $values['parent_id'];
				
				// Create Category
				$categoryId = $tblCategory->createItem($values);
				
				// Add Permission
				if($parentId > 1){
    				$tblResource->addResource('contents.categories.' . $categoryId, $values['title'], 'contents.categories.' . $parentId);
    			}else{
    				$tblResource->addResource('contents.categories.' . $categoryId, $values['title'], 'contents');
    			}
				$tblResource->updatePrivileges('contents.categories.' . $categoryId, (array)$this->_request->getParam('contentsCategories'));

				// Process Task
				if($task == 'edit.close'){
					$this->_helper->_redirector->gotoSimple('index', 'categories', 'contents');
				}elseif($task == 'edit.new'){
					$this->_helper->_redirector->gotoSimple('create', 'categories', 'contents');
				}elseif($task == 'edit.copy'){
					
				}else{
					$this->_helper->_redirector->gotoSimple('edit', 'articles', 'categories', array('id' => $categoryId));
				}
			}
		}
	
		$this->view->form = $form;
		$this->view->permission = Zendvn_Factory::getAcl()->getForm('contents', 'contents.categories', 'contentsCategories', false);
	}
	
	public function ajaxAction(){
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$tblCategory = new Contents_Model_DbTable_Category();
		if(($task = $this->_request->getParam('task')) != null){
			if($task == 'order' && ($id = $this->_request->getParam('id')) != null){
				$parentId = $this->_request->getParam('pid');
				echo $tblCategory->getOrderModal($id, $parentId);
			}
		}
	}
}
