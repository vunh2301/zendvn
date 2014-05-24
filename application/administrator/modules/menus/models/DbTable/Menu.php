<?php

class Menus_Model_DbTable_Menu extends Zendvn_Db_Table_NestedSet
{
	protected $_name	= 'menu';

	protected $_rowsetClass = 'Menus_Model_DbTable_Rowset_Menu';
	
	protected $_rowClass = 'Menus_Model_DbTable_Row_Menu';
	
	protected $_dependentTables = array('Widgets_Model_DbTable_WidgetMenu');
	
	protected $_autoIncrement = null;
	
	protected $_referenceMap    = array(
			'Category' => array(
					'columns'           => array('id'),
					'refTableClass'     => 'Menus_Model_DbTable_Category',
					'refColumns'        => array('id')
			)
	);
	
	private $_globalTimezone;
	
	public function init(){
		$appConfig = Zendvn_Factory::getAppConfig();
		$this->_globalTimezone = $appConfig['site']['timezone'];
	}
	
	
	public function getItems($filter){
		$select = $this->select()->where('lft > 0');
	
		// Filter Search
		if(isset($filter['search']) && null != $filter['search'])
			$select->where('title LIKE("%' . $filter['search'] . '%")');
	
		// Filter Category
		if(isset($filter['menus']))
			$select->where('menu_type_id = ?', $filter['menus']);
	
		// Filter Status
		if(isset($filter['status']) && $filter['status'] != '*')
			$select->where('status = ?', $filter['status']);
	
		// Ordering
		if(null !== $filter['ordering'] && null !== $filter['order_by'])
			$select->order($filter['ordering'] . ' ' . $filter['order_by']);
	
		// Paging
		if(null !== $filter['paginator'] && null !== $filter['paginator_per_page']){
			$adapter 	= new Zend_Paginator_Adapter_DbTableSelect($select);
			$paginator 	= new Zend_Paginator($adapter);
			$paginator->setCurrentPageNumber($filter['paginator'])->setItemCountPerPage($filter['paginator_per_page']);
			return $paginator;
		}else{
			return $this->fetchAll($select);
		}
	}
	
	public function getItem($id){
		$item = $this->fetchRow($this->select()->setIntegrityCheck(false)
				->from('menu')
				->where('menu.id = ?', $id)
				->joinLeft('extensions', 'extensions.id = menu.module_id', array('type_module' => 'extensions.name'))
		);
		$item->params = json_decode($item->params, true);
		$item->query = json_decode($item->query, true);
		return $item;
	}
	
	public function deleteItem($id){
		$acl = Zendvn_Factory::getAcl();
		$tblResource = new Zendvn_Db_Table_AclResource();

		// Remove current permission
		$tblResource->removeNode($acl->get('menus.menuitems.' . $id)->getId());
	
		//Del Branch
		$this->removeNode($id);
	}
	
	public function deleteItems($itemIds){
		foreach ($itemIds as $id){
			$this->deleteItem($id);
		}
	}
	
	public function updateItem($id, $data){
		$preOrder = json_decode($data['pre_order'], true);
		$parentId = $data['parent_id'];
		unset($data['type_module']);
		unset($data['pre_order']);
		unset($data['order']);
		unset($data['parent_id']);
		unset($data['link']);
		unset($data['type_title']);
		unset($data['id']);
		$data['params'] = json_encode($data['params']);
		if(isset($data['query']))
			$data['query'] = json_encode($data['query']);
		
		Zend_Debug::dump($data);
		if($id > 0 && ($item = $this->getNode($id))){
			
			// Update Parent
			if($item->parent_id != $parentId)
				$this->moveNode($id, 'right', $parentId);
			
			// Update Order
			if($preOrder != null){
				foreach ($preOrder as $val){
					$this->moveNode($val['id'], 'right', $parentId);
				}
			}
			
			// Update Status
			if($item->status != $data['status'])
				$this->updateBranch(array('status' => $data['status']), $id);
			
			// Update Old Home
			if($data['home'] == true && $item->home != $data['home']){
				$oldHomeMenu = $this->fetchRow($this->select()->reset()->where('home = ?', true));
				if($oldHomeMenu != null){
					$oldHomeMenu->home = 0;
					$oldHomeMenu->save();
				}
			}
			
			// Update MenuItem
			$this->update($data, $this->_db->quoteInto('id = ?', $id));
		}
	}
	
	public function createItem($data){
		$preOrder = json_decode($data['pre_order'], true);
		$parentId = $data['parent_id'];
		unset($data['type_module']);
		unset($data['pre_order']);
		unset($data['order']);
		unset($data['parent_id']);
		unset($data['link']);
		unset($data['type_title']);
		unset($data['id']);
		$data['params'] = json_encode($data['params']);
		$data['query'] = json_encode($data['query']);
		//Zend_Debug::dump($data);
		
		if($parentId > 0 && ($id = $this->insertNode($data, 'right', $parentId)) > 0){
			// Update Order
			if($preOrder != null){
				foreach ($preOrder as $val){
					if($val['id'] == 0){
						$this->moveNode($id, 'right', $parentId);
					}else{
						$this->moveNode($val['id'], 'right', $parentId);
					}
				}
			}
			// Update Status
			$this->updateBranch(array('status' => $data['status']), $id);
			
			// Update Old Home
    		if($data['home'] == true){
    			$oldHomeMenu = $this->fetchRow($this->select()->reset()->where('home = ?', true));
    			if($oldHomeMenu != null){
    				$oldHomeMenu->home = 0;
    				$oldHomeMenu->save();
    			}
    		}
			return $id;
		}
		
		return false;
	}
	
	public function getMenus($type = 'list'){
		$tblMenus = new Menus_Model_DbTable_Category();
		$items = $tblMenus->fetchAll($tblMenus->select()->order('title'));
		
		if($type == 'rowset') return $items;
		if($items->count() > 0){
			foreach ($items as $item) $list[$item->id] = $item->title;
			return $list;
		}
		return array();
	}
	
	public function getHomeMenusId(){
		$home = $this->fetchRow($this->select()->where('home = ?', 1));
		return $home->menu_type_id;
	}
	
	public function updateStatus(array $items, $status = 'publish'){
		if(count($items) > 0){
			foreach ($items as $item){
				$this->updateBranch(array('status' => $status), $item);
			}
		}
	}
	
	public function getModalModuleType($moduleId = 0, $controller = null){
		$tblExtension = new Extensions_Model_DbTable_Extension();
		$extensions = $tblExtension->fetchAll($tblExtension->select()->where('type = ?', 'module'));
		if($extensions->count() > 0){
			$tabsHtml = '<ul class="nav nav-tabs">';
			$tabsContentHtml = '<div class="tab-content">';
			$activeTab = null;
			foreach ($extensions as $index => $extension){
				$module = $extension->name;
				$moduleTitle = $extension->title;
				if(file_exists($paramsPath = APPLICATION_PATH . "/modules/$module/views/scripts")){
					$_tabsContentHtml = '';
					foreach (scandir($paramsPath) as $viewName) {
						if($viewName != '.' && $viewName != '..'){
							if(file_exists($paramPath = $paramsPath . '/' . $viewName . '/params.xml')){
								$paramConfig = new Zend_Config_Xml($paramPath, null, array('skipExtends' => true,'allowModifications' => true));
								$active = '';
								if($controller != null){
									$active = ($viewName == $controller) ? ' active' : '';
								}
								if($active){
									$_tabsContentHtml .= '<a href="#" class="list-group-item' . $active . '" onclick="return false;">';
								}else{
									$_tabsContentHtml .= '<a href="#" class="list-group-item" onclick="$(\'#type_module\').val(\'' . $module . '\'); $(\'#controller\').val(\'' . $viewName . '\'); $(\'#module_id\').val(\'' . $extension->id . '\'); $(\'#type_title\').val(\'' . $paramConfig->title . '\'); $(\'#task\').val(\'reload\'); $(this).closest(\'form\').submit(); return false;">';
								}
								$_tabsContentHtml .= '<h4 class="list-group-item-heading">' . $paramConfig->title . '</h4>';
								$_tabsContentHtml .= '<p class="list-group-item-text">' . $paramConfig->message . '</p>';
								$_tabsContentHtml .= '</a>';
							}
						}
					}
					if($_tabsContentHtml != ''){
						$activeTab = (!$activeTab && $moduleId == null) ? ' active' : ($extension->id == $moduleId ? ' active' : '');
						$tabsHtml .= '<li class="' . $activeTab . '"><a href="#' . $module . '" data-toggle="tab">' . $moduleTitle . '</a></li>';
						$tabsContentHtml .= '<div class="tab-pane' . $activeTab . '" id="' . $module . '"><div class="list-group">' . $_tabsContentHtml . '</div></div>';
					}
				}
			}
			$tabsContentHtml .= '</div>';
			$tabsHtml .= '</ul>';
			$selectMenuType = '<div class="tabbable tabs-left">' . $tabsHtml . $tabsContentHtml . '<div class="clearfix"></div></div>';
		}
		return $selectMenuType;
	}
	
	public function getParents($menuTypeId, $id = 0, $type = 'list'){
		if($id > 0){
			$node = $this->getNode($id);
			$items = $this->fetchAll($this->select()->where('lft < ' . $node->lft . ' OR lft > ' . $node->rgt . ' OR lft = 0')->where('menu_type_id = ? OR lft = 0', $menuTypeId)->order('lft'));
		}else{
			$items = $this->fetchAll($this->select()->where('menu_type_id = ? OR lft = 0', $menuTypeId)->order('lft'));
		}
		if($type == 'rowset') return $items;
		if($items->count() > 0){
			foreach ($items as $item) $list[$item->id] = str_repeat('|—', $item->level) . ' ' . $item->title;
			return $list;
		}
		return array();
	}
	
	public function getTemplates($type = 'list'){
		$tblTemplate = new Templates_Model_DbTable_Template();
		// Select Template
		$items = $tblTemplate->fetchAll($tblTemplate->select()->setIntegrityCheck(false)
				->from('templates')
				->joinLeft('extensions', 'extensions.name = templates.type', array('template' => 'extensions.title', 'style' => 'templates.title'))
				->where('extensions.location = ?', 'site')
				->order('extensions.title ASC')
				->order('templates.title ASC')
		);
		if($type == 'rowset') return $items;
		if($items->count() > 0){
			foreach ($items as $item) $list[$item->id] = $item->title;
			return $list;
		}
		return array();
	}
	
	public function getOrderModal($id = 0, $orderId = 0, $parentId, $menuTypeId){
		$items = $this->fetchAll($this->select()->where('parent_id = ?', $parentId)->where('menu_type_id = ?', $menuTypeId)->order('lft'));
		$modalOrder = '';
		$hasNode = false;
		//if($items->count() > 0){
			if($id > 0){
				foreach ($items as $index => $item){
					if($orderId != null){
						if($index == $orderId && $currentNode = $this->find($id)->current()){
							$hasNode = true;
							$modalOrder .= '<li class="dd-item active" data-id="' . $currentNode->id . '"><div class="dd-handle">' . $currentNode->title . '</div></li>';				
						}
						if($item->id != $id)
							$modalOrder .= '<li class="dd-item" data-id="' . $item->id . '"><div class="dd-handle">' . $item->title . '</div></li>';
					}else{
						if($item->id == $id){
							$hasNode = true;
							$modalOrder .= '<li class="dd-item active" data-id="' . $item->id . '"><div class="dd-handle">' . $item->title . '</div></li>';
						}else{
							$modalOrder .= '<li class="dd-item" data-id="' . $item->id . '"><div class="dd-handle">' . $item->title . '</div></li>';
						}
					}
				}
				if($hasNode == false && $currentNode = $this->find($id)->current()){
					$modalOrder .= '<li class="dd-item active" data-id="' . $currentNode->id . '"><div class="dd-handle">' . $currentNode->title . '</div></li>';
				}
			}else{
				foreach ($items as $index => $item){
					if($orderId != null && $index == $orderId){
						$modalOrder .= '<li class="dd-item active" data-id="0"><div class="dd-handle">Current Menu Item</div></li>';
					}
					$modalOrder .= '<li class="dd-item" data-id="' . $item->id . '"><div class="dd-handle">' . $item->title . '</div></li>';
				}
				if($orderId == null || $orderId >= $items->count())
					$modalOrder .= '<li class="dd-item active" data-id="0"><div class="dd-handle">Current Menu Item</div></li>';
			}
		//}
		return '<ol class="dd-list">' . $modalOrder . '</ol>';
	}
	
	public function getAutoIncrement(){
		if($this->_autoIncrement === null){
			$status = $this->getAdapter()->query(' SHOW TABLE STATUS LIKE "' . $this->_name . '"')->fetchAll();
			$this->_autoIncrement = (int)$status[0]['Auto_increment'];
		}
		return $this->_autoIncrement;
	}
	
	public function createAlias($alias){
		$marTViet = array("à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă","ằ","ắ","ặ","ẳ","ẵ",
				"è","é","ẹ","ẻ","ẽ","ê","ề","ế","ệ","ể","ễ",
				"ì","í","ị","ỉ","ĩ",
				"ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ","ờ","ớ","ợ","ở","ỡ",
				"ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",
				"ỳ","ý","ỵ","ỷ","ỹ",
				"đ",
				"À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă"
				,"Ằ","Ắ","Ặ","Ẳ","Ẵ",
				"È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",
				"Ì","Í","Ị","Ỉ","Ĩ",
				"Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ"
				,"Ờ","Ớ","Ợ","Ở","Ỡ",
				"Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",
				"Ỳ","Ý","Ỵ","Ỷ","Ỹ",
				"Đ"," ");
		$marKoDau = array("a","a","a","a","a","a","a","a","a","a","a","a","a","a","a","a","a",
				"e","e","e","e","e","e","e","e","e","e","e",
				"i","i","i","i","i",
				"o","o","o","o","o","o","o","o","o","o","o","o","o","o","o","o","o",
				"u","u","u","u","u","u","u","u","u","u","u",
				"y","y","y","y","y",
				"d",
				"A","A","A","A","A","A","A","A","A","A","A","A"
				,"A","A","A","A","A",
				"E","E","E","E","E","E","E","E","E","E","E",
				"I","I","I","I","I",
				"O","O","O","O","O","O","O","O","O","O","O","O"
				,"O","O","O","O","O",
				"U","U","U","U","U","U","U","U","U","U","U",
				"Y","Y","Y","Y","Y",
				"D","-");
		$alias = str_replace($marTViet, $marKoDau, $alias);
		// to url
		$alias = str_replace("@", "-et-", $alias);
		$alias = str_replace("&", "-and-", $alias);
		$alias = preg_replace('~[^\\pL\d]+~u', '-', $alias);
		$alias = trim($alias, '-');
		$alias = iconv('utf-8', 'us-ascii//TRANSLIT', $alias);
		$alias = strtolower($alias);
		$alias = preg_replace('~[^-\w]+~', '', $alias);
		return $alias;
	}
}

?>