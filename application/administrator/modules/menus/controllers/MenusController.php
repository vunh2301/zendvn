<?php
class Menus_MenusController extends Zend_Controller_Action
{


    public function init()
    {
    	//Zend_Session::namespaceUnset('admin.menus.menuitems');

    	$configModule = new Zend_Session_Namespace('admin.menus.menuitems');
    	$configGlobal = new Zend_Session_Namespace('admin.global');
    	
    	$appConfig = Zendvn_Factory::getAppConfig();
		$siteConfig = $appConfig['site'];

		$tblMenuItem = new Menus_Model_DbTable_Menu();
		$homeMenusId = $tblMenuItem->getHomeMenusId();
		
		$configModule->filter['search'] 			= $this->_request->getParam('filter_search', $configModule->filter['search'] ? $configModule->filter['search'] : null);
		$configModule->filter['menus'] 				= $this->_request->getParam('filter_menus') != null ? $this->_request->getParam('filter_menus') : (isset($configModule->filter['menus']) ? $configModule->filter['menus'] : $homeMenusId);
		$configModule->filter['status'] 			= $this->_request->getParam('filter_status') != null ? $this->_request->getParam('filter_status') : (isset($configModule->filter['status']) ? $configModule->filter['status'] : '*');
		$configModule->filter['featured'] 			= $this->_request->getParam('filter_featured') != null ? $this->_request->getParam('filter_featured') : (isset($configModule->filter['featured']) ? $configModule->filter['featured'] : '*');
		$configModule->filter['order_by'] 			= $this->_request->getParam('order_by') != null ? $this->_request->getParam('order_by') : (isset($configModule->filter['order_by']) ? $configModule->filter['order_by'] : 'ASC');
		$configModule->filter['ordering'] 			= $this->_request->getParam('ordering') != null ? $this->_request->getParam('ordering') : (isset($configModule->filter['ordering']) ? $configModule->filter['ordering'] : 'lft');
		$configModule->filter['paginator'] 			= $this->_request->getParam('paginator') != null ? $this->_request->getParam('paginator') : (isset($configModule->filter['paginator']) ? $configModule->filter['paginator'] : 1);
		$configModule->filter['paginator_per_page'] = $this->_request->getParam('paginator_per_page') != null ? $this->_request->getParam('paginator_per_page') : (isset($configModule->filter['paginator_per_page']) ? $configModule->filter['paginator_per_page'] : $siteConfig['recordPerPage']);

		$this->view->filter = $configModule->filter;
    }
	
    public function indexAction()
    {   
    	$this->taskAction();
    	
    	// Data Table
    	$tblMenuItem = new Menus_Model_DbTable_Menu();
    	
    	// Get Paginator
    	$this->view->paginator = $tblMenuItem->getItems($this->view->filter);
    	
    	// Assign to View
    	$this->view->menuItems = $this->view->paginator->getCurrentItems();
    	$this->view->select_menus = $tblMenuItem->getMenus();
    }
    
    public function taskAction(){
    	$task = $this->_request->getParam('task');
    	$tblMenuItem = new Menus_Model_DbTable_Menu();
    	if($task == 'create'){
    		$this->_helper->_redirector->gotoSimple('create', 'menus', 'menus');
    	}elseif($task == 'edit' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
    		$this->_helper->_redirector->gotoSimple('edit', 'menus', 'menus',array('id' => array_shift(array_values($this->view->chekeds))));
    	}elseif(($task == 'publish' || $task == 'unpublish' || $task == 'trash') && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
    		$tblMenuItem->updateStatus($this->view->chekeds, $task);
    	}elseif($task == 'order' && ($orderValues = $this->_request->getParam('pre_order', null)) !== null){	
			$orderValues = json_decode($orderValues, true);
			$parentId = null;
			foreach ($orderValues as $orderValue){
				if(null === $parentId){
					$parentId = $tblMenuItem->getNode($orderValue['id'])->parent_id;
				}
				$tblMenuItem->moveNode($orderValue['id'], 'right', $parentId);
			}
		}elseif($task == 'delete' && ($this->view->chekeds = $this->_request->getParam('record', null)) != null){
			$tblMenuItem->deleteItems($this->view->chekeds);
		}
    }
    
    public function createAction(){
    	$task 		= $this->_request->getParam('task');
    	$module		= $this->_request->getPost('type_module', null);
    	$moduleId 	= $this->_request->getPost('module_id', null);
    	$controller = $this->_request->getPost('controller', null);
    	$subFormMenus = null;
    	$elementsHasRequired = null;
    	if($task == 'close') $this->_helper->_redirector->gotoSimple('index', 'menus', 'menus');
    	Zendvn_Factory::addBreadcrumb(array(array('label' => 'New Menu Item')));
    	
    	// Data Table
    	$tblMenuItem = new Menus_Model_DbTable_Menu();
    	$tblResource = new Zendvn_Db_Table_AclResource();
    	
    	// Form
    	$form = new Menus_Form_MenuItem();
    	
    	// Load Menu Type
    	if($module && $controller){
    		$elementsHasRequired = array();
    		$paramsPath = APPLICATION_PATH . "/modules/$module/views/scripts/$controller/params.xml";
    		$paramsFormPath = APPLICATION_PATH . "/modules/$module/views/scripts/$controller/forms";
    		if(file_exists($paramsPath)){
    			$paramConfig = new Zend_Config_Xml($paramsPath, null, array('skipExtends' => true,'allowModifications' => true));
    			$form->type_title->setValue($paramConfig->title);
    			if(file_exists($paramsFormPath) && $paramConfig->params){
    				$autoloader = new Zend_Application_Module_Autoloader(array('namespace' => '', 'basePath' => $paramsFormPath));
    				$autoloader->addResourceType('params', '', 'Params_Form');
    				foreach ($paramConfig->params as $name => $param){
    					$subFormMenu = new $param->class();
    					$subFormMenu->removeDecorator('HtmlTag');
    					$subFormMenu->removeDecorator('form');
    					$subFormMenu->setIsArray(true);
    					$subFormMenu->setElementsBelongTo('params');
    					$elements = $subFormMenu->getElements();
    					foreach($elements as $element){
    						if($element->isRequired()){
    							$element->setBelongsTo('query');
    							$elementsHasRequired[] = $element->getName();
    							$subFormMenu->removeElement($element->getName());
    							$form->addElement($element);
    						}
    					}
    					$form->addSubForm($subFormMenu, $name . 'SubFormMenu');
    					$subFormMenus[] = $name . 'SubFormMenu';
    				}
    			}
    		}
    	}
    	// Menus
    	$form->menu_type_id->setMultiOptions($tblMenuItem->getMenus());
    	// Add ajax request
    	$form->menu_type_id->setAttrib('onchange',"$('#parent_id').prop('disabled', true); $('[data-target=\"#orderModal\"]').prop('disabled', true); $.post('" . $this->view->url(array('module' => 'menus', 'controller' => 'menus', 'action' => 'ajax'), null, true) . "/task/parent/mid/' + $(this).val(), function(data){\$('#parent_id').html(data); $('#parent_id').prop('disabled', false).change();});");
    	
    	// Parents
    	$menuTypeId = $this->_request->getPost('menu_type_id', array_shift(array_keys($form->menu_type_id->getMultiOptions())));
    	$form->parent_id->setMultiOptions($tblMenuItem->getParents($menuTypeId));
    	// Add ajax request
    	$form->parent_id->setAttrib('onchange', "$('[data-target=\"#orderModal\"]').prop('disabled', true); $.post('" . $this->view->url(array('module' => 'menus', 'controller' => 'menus', 'action' => 'ajax'), null, true) . "/task/order/pid/' + $('#parent_id').val() + '/mid/' + $('#menu_type_id').val(), function(data){\$('.dd').html(data).nestable({maxDepth: 1}); $('#order').val($('.dd .active').index() > 0 ? $('.dd .active').index() : 0);$('[data-target=\"#orderModal\"]').prop('disabled', false);});");   	
    	
    	// Order
    	$parentId = $this->_request->getPost('parent_id', array_shift(array_keys($form->parent_id->getMultiOptions())));
    	$orderId = $this->_request->getPost('order', null);
    	$this->view->order_item = $tblMenuItem->getOrderModal(0, $orderId, $parentId, $menuTypeId);
    	$form->order->setValue($orderId !== null ? $orderId : 0);
    	// Templates
    	$form->template_id->setMultiOptions(array(0 => 'User Default') + (array)$tblMenuItem->getTemplates());
    	
    	// Add Alias Validate -- alias ko được trùng với các menu item cùng parent với nó
    	$validateAlias = new Zend_Validate_Db_NoRecordExists(array('table' => 'menu', 'field' => 'alias'));
    	$validateAlias->setSelect($validateAlias->getSelect()->where('parent_id = ?', $parentId));
    	$form->alias->addValidator($validateAlias);
    	
    	if($this->_request->isPost()){
    		// Check alias
    		$post = $this->_request->getPost();
    		if($post['alias'] == null) $post['alias'] = $post['title'];
    		$post['alias'] = $tblMenuItem->createAlias($post['alias']);
    		if($task != 'reload' && $form->isValid($post)){
    			$values = $form->getValues();
    			//Zend_Debug::dump($values);
    			$itemId = $tblMenuItem->createItem($values);
    			if($itemId != false){
    				// Add Permission
    				if((int)$values['parent_id'] > 1){
    					$tblResource->addResource('menus.menuitems.' . $itemId, $values['title'], 'menus.menuitems.' . $values['parent_id']);
    				}else{
    					$tblResource->addResource('menus.menuitems.' . $itemId, $values['title'], 'menus');
    				}
    				$tblResource->updatePrivileges('menus.menuitems.' . $itemId, (array)$this->_request->getParam('menus_privileges'));
    			}
    		}else{
    			$form->populate($post);
    		}
    	}
    	
    	$this->view->selectMenuType = $tblMenuItem->getModalModuleType($moduleId, $controller);
    	$this->view->subforms = $subFormMenus;
    	$this->view->elementsHasRequired = $elementsHasRequired;
    	$this->view->permission = Zendvn_Factory::getAcl()->getForm('menus', 'menus.menuitems', 'menus_privileges', false);
    	$this->view->form = $form;
    }
    
    public function editAction(){
    	$itemId 				= $this->_request->getParam('id', 0);
    	$task 					= $this->_request->getParam('task');
    	$subFormMenus 			= null;
    	$elementsHasRequired 	= null;
    	if($task == 'close') $this->_helper->_redirector->gotoSimple('index', 'menus', 'menus');
    	Zendvn_Factory::addBreadcrumb(array(array('label' => 'Edit Menu Item')));
    	 
    	// Data Table
    	$tblMenuItem = new Menus_Model_DbTable_Menu();
    	$tblResource = new Zendvn_Db_Table_AclResource();
    	 
    	// Form
    	$form = new Menus_Form_MenuItem();
    	
    	// Get Item
    	$item = $tblMenuItem->getItem($itemId);
    	
    	$module		= $this->_request->getPost('type_module', $item->type_module);
    	$moduleId 	= $this->_request->getPost('module_id', $item->module_id);
    	$controller = $this->_request->getPost('controller', $item->controller);
    	$parentId 	= $this->_request->getPost('parent_id', $item->parent_id);
    	$menuTypeId = $this->_request->getPost('menu_type_id', $item->menu_type_id);
    	$orderId 	= $this->_request->getPost('order', ($order = $tblMenuItem->getNodeOrdering($itemId, $parentId)) !== null ? $order : 0);
    	
    	// Load Menu Type
    	if($module && $controller){
    		$elementsHasRequired = array();
    		$paramsPath = APPLICATION_PATH . "/modules/$module/views/scripts/$controller/params.xml";
    		$paramsFormPath = APPLICATION_PATH . "/modules/$module/views/scripts/$controller/forms";
    		if(file_exists($paramsPath)){
    			$paramConfig = new Zend_Config_Xml($paramsPath, null, array('skipExtends' => true,'allowModifications' => true));
    			$form->type_title->setValue($paramConfig->title);
    			if(file_exists($paramsFormPath) && $paramConfig->params){
    				$autoloader = new Zend_Application_Module_Autoloader(array('namespace' => '', 'basePath' => $paramsFormPath));
    				$autoloader->addResourceType('params', '', 'Params_Form');
    				foreach ($paramConfig->params as $name => $param){
    					$subFormMenu = new $param->class();
    					$subFormMenu->removeDecorator('HtmlTag');
    					$subFormMenu->removeDecorator('form');
    					$subFormMenu->setIsArray(true);
    					$subFormMenu->setElementsBelongTo('params');
    					$elements = $subFormMenu->getElements();
    					foreach($elements as $element){
    						if($element->isRequired()){
    							$element->setBelongsTo('query');
    							$elementsHasRequired[] = $element->getName();
    							$subFormMenu->removeElement($element->getName());
    							$form->addElement($element);
    						}
    					}
    					$form->addSubForm($subFormMenu, $name . 'SubFormMenu');
    					$subFormMenus[] = $name . 'SubFormMenu';
    				}
    			}
    		}
    	}
    	
    	// Menus
    	$form->menu_type_id->setMultiOptions($tblMenuItem->getMenus());
    	// Add ajax request
    	$form->menu_type_id->setAttrib('onchange',"$('#parent_id').prop('disabled', true); $('[data-target=\"#orderModal\"]').prop('disabled', true); $.post('" . $this->view->url(array('module' => 'menus', 'controller' => 'menus', 'action' => 'ajax'), null, true) . "/task/parent/mid/' + $(this).val(), function(data){\$('#parent_id').html(data); $('#parent_id').prop('disabled', false).change();});");
    	 
    	// Parents
    	$form->parent_id->setMultiOptions($tblMenuItem->getParents($menuTypeId, $itemId));
    	// Add ajax request
    	$form->parent_id->setAttrib('onchange', "$('[data-target=\"#orderModal\"]').prop('disabled', true); $.post('" . $this->view->url(array('module' => 'menus', 'controller' => 'menus', 'action' => 'ajax'), null, true) . "/task/order/id/" . $itemId . "/pid/' + $('#parent_id').val() + '/mid/' + $('#menu_type_id').val(), function(data){\$('.dd').html(data).nestable({maxDepth: 1}); $('#order').val($('.dd .active').index() > 0 ? $('.dd .active').index() : 0);$('[data-target=\"#orderModal\"]').prop('disabled', false);});");
    	 
    	// Order
    	$this->view->order_item = $tblMenuItem->getOrderModal($itemId, $orderId, $parentId, $menuTypeId);
    	$form->order->setValue($orderId);
    	
    	// Templates
    	$form->template_id->setMultiOptions(array(0 => 'User Default') + (array)$tblMenuItem->getTemplates());
    	 
    	// Add Alias Validate -- alias ko được trùng với các menu item cùng parent với nó ngoại trừ chính nó
    	$validateAlias = new Zend_Validate_Db_NoRecordExists(array('table' => 'menu', 'field' => 'alias', 'exclude' => array(
    				'field' => 'id',
    				'value' => $itemId
    	)));
    	$validateAlias->setSelect($validateAlias->getSelect()->where('parent_id = ?', $parentId));
    	$form->alias->addValidator($validateAlias);
    	 
    	if($this->_request->isPost()){
    		// Check alias
    		$post = $this->_request->getPost();
    		if($post['alias'] == null) $post['alias'] = $post['title'];
    		$post['alias'] = $tblMenuItem->createAlias($post['alias']);
    		if($task != 'reload' && $form->isValid($post)){
    			$values = $form->getValues();
    			$tblMenuItem->updateItem($itemId, $values);
    		}else{
    			$form->populate($post);
    		}
    	}else{
    		$form->populate($item->toArray());
    	}
    	 
    	$this->view->selectMenuType 		= $tblMenuItem->getModalModuleType($moduleId, $controller);
    	$this->view->subforms 				= $subFormMenus;
    	$this->view->elementsHasRequired 	= $elementsHasRequired;
    	$this->view->permission 			= Zendvn_Factory::getAcl()->getForm('menus.menuitems.' . $itemId, 'menus.menuitems', 'menus_privileges');
    	$this->view->form 					= $form;
    }
    
    public function ajaxAction(){
    	$this->view->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	if(($task = $this->_request->getParam('task')) != null){
    		$tblMenuItem = new Menus_Model_DbTable_Menu();
    		$id = (int)$this->_request->getParam('id', 0);
    		if($task == 'order'){
    			$parentId = $this->_request->getParam('pid', 0);
    			$mid = $this->_request->getParam('mid', 0);
    			$orderId = $this->_request->getPost('oid', 0);
    			$modalOrder = $tblMenuItem->getOrderModal($id, $orderId, $parentId, $mid);
    			echo $modalOrder;
    		}elseif($task == 'parent' && ($mid = $this->_request->getParam('mid')) != null){
    			$items = $tblMenuItem->getParents($mid, $id, 'rowset');
    			if($items->count() > 0){
    				foreach ($items as $item){
    					echo '<option value="' . $item->id . '">' . str_repeat('|—', $item->level) . ' ' . $item->title . '</option>';
    				}
    			}
    		}
    	}
    }
}
