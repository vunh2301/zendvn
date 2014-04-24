<?php

class Users_Model_DbTable_Group extends Zendvn_Db_Table_NestedSet
{
	protected $_name	= 'groups';
	
	protected $_dependentTables = array('Users_Model_DbTable_UserGroup', 'Zendvn_Db_Table_AclPrivilege');
	
	public function getItems($filter){
		$select = $this->select()->from('groups')
		->setIntegrityCheck(false)
		->joinLeft('user_group', 'user_group.group_id = groups.id', 	array(
				'users' => new Zend_Db_Expr("SUM(case when user_group.group_id = groups.id then 1 else 0 end)")
		))
		->group('groups.id');
	
		// Filter Search
		if(null != $filter['search'])
			$select->where('groups.title LIKE("%' . $filter['search'] . '%")');
	
		// Filter State
		if($filter['state'] != '*')
			$select->where('groups.protected = ?', $filter['state']);
	
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
		return $this->find($id)->current();
	}
	
	public function deleteItem($id){
		if(($item = $this->find($id)->current()) === null)return false;		
		if($item->protected == true) return false;

		$groupChilds = $this->getChilds($id);
		foreach($groupChilds as $child){
			$this->_deleteUsers($child->id);
		}
		
		$this->_deleteUsers($item->id);
		$this->removeNode($id);
	}
	
	public function deleteItems($itemIds){
		foreach ($itemIds as $id){
			$this->deleteItem($id);
		}
	}
	
	private function _deleteUsers($id){
		// Delete All Users
		$tblUser = new Users_Model_DbTable_User();
		$users = $tblUser->getItems(array('group' => $id));
		foreach ($users as $user){
			$tblUser->deleteItem($user->id);
		}
	}
	
	public function getParents($id = 0, $type = 'list'){
		if($id > 0){
			$node = $this->getNode($id);
			$items = $this->fetchAll($this->select()->where('lft < ' . $node->lft . ' OR lft > ' . $node->rgt)->order('lft'));
		}else{
			$items = $this->fetchAll($this->select()->order('lft'));
		}
		if($type == 'rowset') return $items;
		if($items->count() > 0){
			foreach ($items as $item) $list[$item->id] = str_repeat('|—', $item->level) . ' ' . $item->title;
			return $list;
		}
		return array();
	}
}

?>