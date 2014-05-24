<?php

class Menus_Model_DbTable_Category extends Zendvn_Db_Table_Abstract
{
	protected $_name	= 'menu_type';
	
	protected $_referenceMap    = array(
			'Menu' => array(
					'columns'           => array('id'),
					'refTableClass'     => 'Menus_Model_DbTable_Menu',
					'refColumns'        => array('id')
			)
	);
	
	public function getItems($filter){
	
		$select = $this->select()
		->from('menu_type')
		->setIntegrityCheck(false)
		->joinLeft('menu', 'menu.menu_type_id = menu_type.id', 	array(
				'publish' 	=> new Zend_Db_Expr("SUM(case when menu.status = 'publish' then 1 else 0 end)"),
				'unpublish' => new Zend_Db_Expr("SUM(case when menu.status = 'unpublish' then 1 else 0 end)"),
				'home' => new Zend_Db_Expr("SUM(case when menu.home = 1 then 1 else 0 end)")
		))
		->group('menu_type.id');
		
		
		// Filter Search
		if(isset($filter['search']) && null != $filter['search'])
			$select->where('menu_type.title LIKE("%' . $filter['search'] . '%")');
	
		// Ordering
		if(isset($filter['ordering']) && isset($filter['order_by']) && null !== $filter['ordering'] && null !== $filter['order_by'])
			$select->order($filter['ordering'] . ' ' . $filter['order_by']);
	
		// Paging
		if(isset($filter['paginator']) && isset($filter['paginator_per_page'])){
			$adapter 	= new Zend_Paginator_Adapter_DbTableSelect($select);
			$paginator 	= new Zend_Paginator($adapter);
			$paginator->setCurrentPageNumber($filter['paginator'])->setItemCountPerPage($filter['paginator_per_page']);
			return $paginator;
		}else{
			return $this->fetchAll($select);
		}
	}

	public function getItem($id){
		return $this->fetchRow($this->select()
			->from('menu_type')
			->setIntegrityCheck(false)
			->where('menu_type.id = ?', $id)
			->joinLeft('menu', 'menu.menu_type_id = menu_type.id', 	array(
					'publish' 	=> new Zend_Db_Expr("SUM(case when menu.status = 'publish' then 1 else 0 end)"),
					'unpublish' => new Zend_Db_Expr("SUM(case when menu.status = 'unpublish' then 1 else 0 end)"),
					'home' => new Zend_Db_Expr("SUM(case when menu.home = 1 then 1 else 0 end)")
			))
			->group('menu_type.id')
		);
	}
	
	public function deleteItem($id){
		$item = $this->getItem($id);
		if(null === $item || $item->home == true) return false;
		
		$this->_deleteMenuItems($id);
		$item->delete();	
	}
	
	public function deleteItems($itemIds){
		foreach ($itemIds as $id){
			$this->deleteItem($id);
		}
	}

	public function updateItem($id, $data){
		if($id > 0){
			$item = $this->find($id)->current()->setFromArray($data);
			return $item->save();
		}
	}
	
	public function createItem($data){
		$item = $this->createRow($data);
		return $item->save();
	}
	
	public function copyItem($id){
		$item = $this->find($id)->current();
		if($item){
			$data = $item->toArray();
			unset($data['id']);
			$data['title'] = $this->copyTitle($data['title']);
			$newItem = $this->createRow($data);
			return $newItem->save();
		}
	}
	
	private function _deleteMenuItems($id){
		// Delete All Menu Items
		$tblMenuItem = new Menus_Model_DbTable_Menu();
		$menuItems = $tblMenuItem->getItems(array('menu_type_id' => $id));
		foreach ($menuItems as $menuItem){
			$tblMenuItem->deleteItem($menuItem->id);
		}
	}
	
	private function copyTitle($title){
		$matches = array();
		preg_match_all('/' . preg_quote(' (copy ', '/') . '(\d+)'. preg_quote(')', '/').'/i', $title, $matches);
		if($matches[1] != null){
			$title = str_replace($matches[0][0],	' (copy ' . ((integer)$matches[1][0] + 1) . ')', $title);
		}else{
			$title = $title . ' (copy 1)';
		}
		return $title;
	}
}

?>