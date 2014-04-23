<?php
class Contents_ArticlesController extends Zendvn_Controller_Action
{	
	public function init(){
		$configModule = new Zend_Session_Namespace('admin.contents.articles');
		$configGlobal = new Zend_Session_Namespace('admin.global');
		
		$appConfig = Zendvn_Factory::getAppConfig();
		$siteConfig = $appConfig['site'];

		$configModule->filter['search'] 	= $this->_request->getParam('filter_search', $configModule->filter['search'] ? $configModule->filter['search'] : null);
		$configModule->filter['category'] 	= $this->_request->getParam('filter_category', '*') != null ? $this->_request->getParam('filter_category', '*') : $configModule->filter['category'];
		$configModule->filter['status'] 	= $this->_request->getParam('filter_status', '*') != null ? $this->_request->getParam('filter_status', '*') : $configModule->filter['status'];
		$configModule->filter['featured'] 	= $this->_request->getParam('filter_featured', '*') != null ? $this->_request->getParam('filter_featured', '*') : $configModule->filter['featured'];
		$configModule->filter['order_by'] 	= $this->_request->getParam('order_by', 'ASC') != null ? $this->_request->getParam('order_by', 'ASC') : $configModule->filter['order_by'];
		$configModule->filter['ordering'] 	= $this->_request->getParam('ordering', 'title') != null ? $this->_request->getParam('ordering', 'title') : $configModule->filter['ordering'];
		$configModule->filter['paginator'] 	= $this->_request->getParam('paginator', 1) ? $this->_request->getParam('paginator', 1) : $configModule->filter['paginator'];
		$configModule->filter['paginator_per_page'] = $this->_request->getParam('paginator_per_page', $siteConfig['recordPerPage']) != null ? $this->_request->getParam('paginator_per_page', $siteConfig['recordPerPage']) : $configModule->filter['paginator_per_page'];

		$this->view->filter = $configModule->filter;
	}
	
	public function indexAction(){		
		// Add js	
		$this->view->headScript()->appendFile($this->view->baseUrl("/templates/admin/js/holder.js"));
		
		// Other Task
		$this->taskAction();
	
		// Data Table
		$tblArticle = new Contents_Model_DbTable_Article();
		
		// Get Paginator
		$this->view->paginator = $tblArticle->getItems($this->view->filter);
		
		// Assign to View
		$this->view->articles = $this->view->paginator->getCurrentItems();
		$this->view->select_categories = $tblArticle->getCategories();
	}
	
	public function taskAction(){
		$task = $this->_request->getParam('task');
		$tblArticle = new Contents_Model_DbTable_Article();
		if($task == 'create'){
			$this->_helper->_redirector->gotoSimple('create', 'articles', 'contents');
		}elseif($task == 'edit' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
			$this->_helper->_redirector->gotoSimple('edit', 'articles', 'contents',array('id' => array_shift(array_values($this->view->chekeds))));
		}elseif(($task == 'publish' || $task == 'unpublish' || $task == 'trash') && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
			$tblArticle->updateStatus($this->view->chekeds, $task);
		}elseif(($task == 'featured' || $task == 'unfeatured') && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
			$tblArticle->updateFeatured($this->view->chekeds, ($task == 'featured'));
		}elseif($task == 'delete' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){	
			foreach ($this->view->chekeds as $recordId){
				$tblArticle->deleteItem($recordId);
			}
		}
	}
	
	public function createAction(){
		$articleId = $this->_request->getParam('id', 0);
		$task = $this->_request->getParam('task');
		if($task == 'close') $this->_helper->_redirector->gotoSimple('index', 'articles', 'contents');
		Zendvn_Factory::addBreadcrumb(array(array('label' => 'New Article')));
		
		// Data Table
		$tblResource 	= new Zendvn_Db_Table_AclResource();
		$tblArticle 	= new Contents_Model_DbTable_Article();
		$selectCat		= $tblArticle->getCategories();
		$form 			= new Contents_Form_Article();
		
		$form->category_id->setMultiOptions($selectCat);
		
		$categoryId = $this->_request->getPost('category_id');
		//Add Alias Validate
		$validateAlias = new Zend_Validate_Db_NoRecordExists(array('table' => 'articles','field' => 'alias'));
		$validateAlias->setSelect($validateAlias->getSelect()->where('category_id = ?', $categoryId));
		$form->alias->addValidator($validateAlias);
		
		
		if($this->_request->isPost()){
			// Check alias
			$post = $this->_request->getPost();
			if($post['alias'] == null) $post['alias'] = $post['title'];
			$post['alias'] = $tblArticle->createAlias($post['alias']);
			// Check validate
			if($form->isValid($post)){
				$values = array_shift(array_values($form->getValues()));
				// Upload Image
				if ($form->image->isUploaded()) {
					$values['image'] = $tblArticle->updateImage($values['image']);
				}else{
					unset($values['image']);
				}
				 
				// Update
				$articleId = $tblArticle->createItem($values);
				// Update Permission
				if((int)$values['category_id'] > 0){
					$tblResource->addResource('contents.articles.' . $articleId, $values['title'], 'contents.categories.' . $values['category_id']);
				}
				$tblResource->updatePrivileges('contents.articles.' . $articleId, (array)$this->_request->getParam('contentsArticles'));
				 
				// Process Task
				if($task == 'edit.close'){
					$this->_helper->_redirector->gotoSimple('index', 'articles', 'contents');
				}elseif($task == 'edit.new'){
					$this->_helper->_redirector->gotoSimple('create', 'articles', 'contents');
				}elseif($task == 'edit.copy'){
   					$articleId = $tblArticle->copyItem($articleId);
   					// Update Permission
   					if((int)$values['category_id'] > 0){
   						$tblResource->addResource('contents.articles.' . $articleId, $values['title'], 'contents.categories.' . $values['category_id']);
   					}
   					$tblResource->updatePrivileges('contents.articles.' . $articleId, $resourceData);
   					$this->_helper->_redirector->gotoSimple('edit', 'articles', 'contents', array('id' => $articleId));
   				}else{
					$this->_helper->_redirector->gotoSimple('edit', 'articles', 'contents', array('id' => $articleId));
				}
			}
		}
		$this->view->form = $form;
		$this->view->permission = Zendvn_Factory::getAcl()->getForm('contents', 'contents.articles', 'contentsCategories', false);
		
	}
	
	public function editAction(){
		$articleId = $this->_request->getParam('id', 0);
		$task = $this->_request->getParam('task');
		if($task == 'close') $this->_helper->_redirector->gotoSimple('index', 'articles', 'contents');
		Zendvn_Factory::addBreadcrumb(array(array('label' => 'Edit Article')));
		
		// Data Table
		$tblResource 	= new Zendvn_Db_Table_AclResource();
		$tblArticle 	= new Contents_Model_DbTable_Article();
		$form 			= new Contents_Form_Article();
		$article 		= $tblArticle->getItem($articleId);
		$selectCat		= $tblArticle->getCategories();
		
		$form->populate($article->toArray());
		
		$form->category_id->setMultiOptions($selectCat);
		
		if($article->image)$form->image->setAttrib('src', $this->view->baseUrl('/modules/contents/images/thumbnails/' . $article->image));
		$categoryId = $this->_request->getPost('category_id', $article->category_id);
		//Add Alias Validate
		$validateAlias = new Zend_Validate_Db_NoRecordExists(array('table' => 'articles','field' => 'alias', 'exclude' => array(
				'field' => 'id',
				'value' => $articleId
		)));
		$validateAlias->setSelect($validateAlias->getSelect()->where('category_id = ?', $categoryId));
		$form->alias->addValidator($validateAlias);

		
		if($this->_request->isPost()){
			// Check alias after check validate
			$post = $this->_request->getPost();
			if($post['alias'] == null) $post['alias'] = $post['title'];
			$post['alias'] = $tblArticle->createAlias($post['alias']);
			// Check validate
    		if($form->isValid($post)){
    			$values = array_shift(array_values($form->getValues()));
    			// Upload Image
    			if ($form->image->isUploaded()) {
    				$imagePath = PUBLISH_PATH . '/modules/contents/images/';
    				if(is_file($imagePath . $article->image))unlink($imagePath . $article->image);
    				if(is_file($imagePath . 'thumbnails/' .  $article->image))unlink($imagePath . 'thumbnails/' .  $article->image); 				
    				$values['image'] = $tblArticle->updateImage($values['image']);
    			}else{
    				unset($values['image']);
    			}
    			
    			// Update
   				$tblArticle->updateItem($articleId, $values);
	   			//Update Permission
    			if($values['category_id'] != $article->category_id){
    				$tblResource->moveResource('contents.articles.' . $articleId, 'contents.categories.' . $values['category_id']);
    			}
    			$tblResource->updatePrivileges('contents.articles.' . $articleId, (array)$this->_request->getParam('contentsArticles'));
    			
   				// Process Task
   				if($task == 'edit.close'){
   					$this->_helper->_redirector->gotoSimple('index', 'articles', 'contents');
   				}elseif($task == 'edit.new'){
   					$this->_helper->_redirector->gotoSimple('create', 'articles', 'contents');
   				}elseif($task == 'edit.copy'){
   					$articleId = $tblArticle->copyItem($articleId);
   					// Update Permission
   					if((int)$values['category_id'] > 0){
   						$tblResource->addResource('contents.articles.' . $articleId, $values['title'], 'contents.categories.' . $values['category_id']);
   					}
   					$tblResource->updatePrivileges('contents.articles.' . $articleId, (array)$this->_request->getParam('contentsArticles'));
   					$this->_helper->_redirector->gotoSimple('edit', 'articles', 'contents', array('id' => $articleId));
   				}else{
					$this->_helper->_redirector->gotoSimple('edit', 'articles', 'contents', array('id' => $articleId));
				}
			}
		}
		$this->view->form = $form;
		$this->view->permission = Zendvn_Factory::getAcl()->getForm('contents.articles.' . $articleId, 'contents.articles', 'contentsArticles');
	}

}
