<?php

class Contents_Model_DbTable_Category extends Zendvn_Db_Table_NestedSet
{
	protected $_name		= 'categories';

	protected $_rowsetClass = 'Contents_Model_DbTable_Rowset_Categories';
	
	protected $_rowClass 	= 'Contents_Model_DbTable_Row_Category';
	
	private $_globalTimezone;
	
	public function init(){
		$appConfig = Zendvn_Factory::getAppConfig();
		$this->_globalTimezone = $appConfig['site']['timezone'];
	}
	
	public function getItems($filter){
		$select = $this->select()->where('lft > 0');
	
		// Filter Search
		if(null != $filter['search'])
			$select->where('title LIKE("%' . $filter['search'] . '%")');
	
		// Filter Category
		if($filter['level'] != '*')
			$select->where('level <= ?', $filter['level']);
	
		// Filter Status
		if($filter['status'] != '*')
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
				->from('categories')
				->where('categories.id = ?', $id)
				->joinLeft('users', 'categories.created_user_id = users.id', array('created_user' => 'users.real_name'))
				->joinLeft('users', 'categories.modified_user_id = users.id', array('modified_user' => 'users.real_name'))
				->group('categories.id')
		);
			
		$item->metadata = json_decode($item->metadata, true);
		return $item;
	}
	
	public function deleteItem($id){
		//
	}
	
	public function updateItem($id, $data){
		$user = Zendvn_Factory::getUser();
		if($data['alias'] == null) $data['alias'] = $this->createAlias($data['title']);
		$data['modified_user_id'] = $user->id;
		$date = new Zend_Date();
		$data['modified_date'] = $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
		$data['metadata'] = json_encode($data['metadata']);
	
		unset($values['parent_id']);
    	unset($values['pre_order']);
    	unset($values['order']);
    	unset($values['created_date']);
    	unset($values['created_user']);
    	unset($values['created_user_id']);
    	unset($values['modified_date']);
    	unset($values['modified_user']);
    	unset($values['id']);
    	
		if($id > 0){
			// Update Parent
			$tblCategory->moveNode($id, 'right', $values['parent_id']);
			// Update Order
			if(($orderValues = $values['pre_order']) != null){
				$orderValues = json_decode($orderValues, true);
				foreach ($orderValues as $orderValue){
					$this->moveNode($orderValue['id'], 'right', $values['parent_id']);
				}
			}
			// Update Status
			$this->updateBranch(array('status' => $values['status']), $id);
			// Update category
			$this->update($values, $this->_db->quoteInto('id = ?', $id));
		}
	}

	public function getParents($category, $type = 'list'){
		$items = $this->fetchAll($this->select()->where('lft < ' . $category->lft . ' OR lft > ' . $category->rgt)->order('lft'));
		if($type == 'rowset') return $items;
		if($items->count() > 0){
			foreach ($items as $item) $list[$item->id] = str_repeat('|—', $item->level) . ' ' . $item->title;
			return $list;
		}
		return array();
	}
	
	public function getOrderModal($id, $parentId){
		$parentId = $parentId ? $parentId : $this->getNode($id)->parent_id;
		$items = $this->fetchAll($this->select()->where('parent_id = ?', $parentId)->order('lft'));
		$modalOrder = '';
		$hasNode = false;
		if($items->count() > 0){
			foreach ($items as $item){
				if($item->id == $id){
					$hasNode = true;
					$modalOrder .= '<li class="dd-item active" data-id="' . $item->id . '"><div class="dd-handle">' . $item->title . '</div></li>';
				}else{
					$modalOrder .= '<li class="dd-item" data-id="' . $item->id . '"><div class="dd-handle">' . $item->title . '</div></li>';
				}
			}
		}
		if(!$hasNode){
			if($currentNode = $this->find($id)->current()){
				$modalOrder .= '<li class="dd-item active" data-id="' . $id . '"><div class="dd-handle">' . $currentNode->title . '</div></li>';
			}else{
				$modalOrder .= '<li class="dd-item active" data-id="0"><div class="dd-handle">Current Category</div></li>';
			}
		}
		return '<ol class="dd-list">' . $modalOrder . '</ol>';
	}
	
	public function updateStatus(array $items, $status = 'publish'){
		$items = $this->find($items);
		if($items->count() > 0){
			foreach ($items as $item){
				$item->status = $status;
				$item->save();
			}
		}
	}

	public function generateRandomString($length = 50) {
		$strings = '0123456789abcdefghijklmnopqrstuvwxyz';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $strings[rand(0, strlen($strings) - 1)];
		}
		return $randomString;
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