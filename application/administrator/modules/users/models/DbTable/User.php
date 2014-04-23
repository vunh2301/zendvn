<?php

class Users_Model_DbTable_User extends Zendvn_Db_Table_Abstract
{
	protected $_name	= 'users';
	
 	protected $_rowClass = 'Users_Model_DbTable_Row_User';
	
 	protected $_dependentTables = array('Users_Model_DbTable_UserGroup');
 	
 	private $_globalTimezone;
 	
 	protected $_referenceMap = array(
 			'UserGroup' => array(
 					'columns'           => array('id'),
 					'refTableClass'     => 'Users_Model_DbTable_UserGroup',
 					'refColumns'        => array('user_id'),
 					'onDelete'          => self::CASCADE,
 					'onUpdate'          => self::RESTRICT
 			)
 	);
  	
 	public function init(){
 		$appConfig = Zendvn_Factory::getAppConfig();
 		$this->_globalTimezone = $appConfig['site']['timezone'];
 	}
 	
 	public function getItems($filter){
 		$select = $this->select();

 		// Filter Search
 		if(null != $filter['search'])
 			$select->where('real_name LIKE("%' . $filter['search'] . '%")');
 	
 		// Filter State
 		if($filter['state'] != '*')
 			$select->where('block = ?', $filter['state']);
 	
 		// Filter Status
 		if($filter['status'] != '*')
 			$select->where('active = ?', $filter['status']);
 	
 		// Filter Group
 		if($filter['group'] != '*')
 			$select->setIntegrityCheck(false)
			->from('users')
			->joinleft('user_group', 'users.id = user_group.user_id', array('group_id'))
			->where('user_group.group_id = ?', $filter['group']);
 	
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
 		$item = $this->find($id)->current();
 		$item->params = json_decode($item->params, true);
 		return $item;
 	}
 	
 	public function deleteItem($id){
 		$item = $this->find($id)->current();
 		$item->delete();
 	}
  	
 	public function createItem($data){
 		// Password
 		if($data['password_confirm'] != null){
 			$data['password_salt'] = $this->generateRandomString();
 			$data['password'] = MD5($data['password_confirm'] . $data['password_salt']);
 		}else{
 			unset($data['password']);
 		}
 		// Params
 		$data['params'] = json_encode($data['params']);
 		
 		if($data['active'] == false){
 			$data['active'] = $this->generateRandomString(100);
 		}
 		$date = new Zend_Date();
 		$data['register_date'] = $data['last_visit_date'] = $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
 		
 		$id = $this->createRow($data)->save();
 		
 		if($id > 0){
 			//Update UserGroup
 			if(is_array($data['groups'])){
 				$tblUserGroup = new Users_Model_DbTable_UserGroup();
 				$groups = $tblUserGroup->fetchAll($tblUserGroup->select()->where('user_id = ?', $id));
 				if($groups->count() > 0){
 					foreach ($groups as $group){
 						if(in_array($group->group_id, $data['groups'])){
 							//edit
 							$data['groups'] = array_diff( $data['groups'], array($group->group_id));
 						}else{
 							//remove
 							$group->delete();
 						}
 					}
 				}
 				//insert
 				foreach($data['groups'] as $groupInsert){
 					$tblUserGroup->insert(array(
 							'user_id' => $id,
 							'group_id' => $groupInsert
 					));
 				}
 			}
 		}
 		return $id;
 	}
 	
 	public function updateItem($id, $data){
 		// Password
 		if($data['password_confirm'] != null){
 			$data['password_salt'] = $this->generateRandomString();
 			$data['password'] = MD5($data['password_confirm'] . $data['password_salt']);
 		}else{
 			unset($data['password']);
 		}
 		// Params
 		$data['params'] = json_encode($data['params']);

 		if($data['active'] == false){
 			$data['active'] = $this->generateRandomString(100);
 		}
 		
 		if($data['register_date'] != null){
 			$date = new Zend_Date($data['register_date']);
 			$data['register_date'] = $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
 		}
 		if($data['last_visit_date'] != null){
 			$date = new Zend_Date($data['last_visit_date']);
 			$data['last_visit_date'] = $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
 		}
 		
 		unset($data['id']);
 		if($id > 0){
 			//Update UserGroup
 			if(is_array($data['groups'])){
 				$tblUserGroup = new Users_Model_DbTable_UserGroup();
 				$groups = $tblUserGroup->fetchAll($tblUserGroup->select()->where('user_id = ?', $id));
 				if($groups->count() > 0){
	 				foreach ($groups as $group){
	 					if(in_array($group->group_id, $data['groups'])){
	 						//edit
	 						$data['groups'] = array_diff( $data['groups'], array($group->group_id));
	 					}else{
	 						//remove
	 						$group->delete();
	 					}
	 				}
 				}
 				//insert
 				foreach($data['groups'] as $groupInsert){
 					$tblUserGroup->insert(array(
 							'user_id' => $id,
 							'group_id' => $groupInsert
 					));
 				}
 			}
 			
 			$user = $this->find($id)->current()->setFromArray($data);
 			return $user->save();
 		}
 	}
 	
 	public function updateState(array $items, $state = false){
 		$rows = $this->find($items);
		if($rows->count() > 0){
			foreach ($rows as $row){
				$row->block = $state;
				$row->save();
			}
		}
 	}
 	
 	public function updateActivate(array $items){
 		$rows = $this->find($items);
 		if($rows->count() > 0){
 			foreach ($rows as $row){
 				$row->active = true;
 				$row->save();
 			}
 		}
 	}
 	
 	public function getTemplates($type = 'list'){
 		$tblTemplate = new Zendvn_Db_Table_Template();
 		$items = $tblTemplate->fetchAll($tblTemplate->select()->setIntegrityCheck(false)
 				->from('templates')
 				->joinLeft('extensions', 'extensions.name = templates.type', array('template' => 'extensions.title', 'style' => 'templates.title'))
 				->where('extensions.session = ?', 'site')
 				->order('extensions.title ASC')
 				->order('templates.title ASC')
 		);
 		if($type == 'rowset') return $items;
 		if($items->count() > 0){
 			foreach ($items as $item) $list[$item->template][$item->id] = $item->style;
 			return $list;
 		}
 		return array();
 	}
 	
 	public function getGroups($type = 'list'){
 		$tblGroup = new Users_Model_DbTable_Group();
 		$items = $tblGroup->fetchAll($tblGroup->select()->order('lft'));

 		if($type == 'rowset') return $items;
 		if($items->count() > 0){
 			foreach ($items as $item) $list[$item->id] = str_repeat('|— ', $item->level) . ' ' . $item->title;
 			return $list;
 		}
 		return array();
 	}
 	
 	public function generateRandomString($length = 50) {
 		$strings = '0123456789abcdefghijklmnopqrstuvwxyz';
 		$randomString = '';
 		for ($i = 0; $i < $length; $i++) {
 			$randomString .= $strings[rand(0, strlen($strings) - 1)];
 		}
 		return $randomString;
 	}
}