<?php
require_once APPLICATION_PATH . '/library/Wideimage/Wideimage.php';
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
		$acl = Zendvn_Factory::getAcl();
		$tblResource = new Zendvn_Db_Table_AclResource();
		if(($item = $this->find($id)->current()) === null)return false;
		
		//Del articles of current cat
		$this->_deleteArticles($item->id);
		
		// Delete Image
		if(is_file(PUBLISH_PATH . '/modules/contents/images/' . $item->image))unlink(PUBLISH_PATH . '/modules/contents/images/' . $item->image);
		if(is_file(PUBLISH_PATH . '/modules/contents/images/thumbnails/' . $item->image))unlink(PUBLISH_PATH . '/modules/contents/images/thumbnails/' . $item->image);
		
		//Find and del articles + permission of childs Cat
		$catChilds = $this->getChilds($id);
		foreach($catChilds as $catChild){
			// Remove article
			$this->_deleteArticles($catChild->id);
			
			// Delete Image
			if(is_file(PUBLISH_PATH . '/modules/contents/images/' . $catChild->image))unlink(PUBLISH_PATH . '/modules/contents/images/' . $catChild->image);
			if(is_file(PUBLISH_PATH . '/modules/contents/images/thumbnails/' . $catChild->image))unlink(PUBLISH_PATH . '/modules/contents/images/thumbnails/' . $catChild->image);
			
		}
		// Remove current permission
		$tblResource->removeNode($acl->get('contents.categories.' . $id)->getId());
		
		//Del Branch
		$this->removeNode($id);
	}
	
	public function deleteItems($itemIds){
		foreach ($itemIds as $id){
			$this->deleteItem($id);
		}
	}

	private function _deleteArticles($id){
		// Delete All Articles
		$tblArticle = new Contents_Model_DbTable_Article();
		$articles = $tblArticle->getItems(array('category'=>$id));
		foreach ($articles as $article){
			$tblArticle->deleteItem($article->id);
		}
	}
	
	public function updateItem($id, $data){
		$user = Zendvn_Factory::getUser();
		$data['modified_user_id'] 	= $user->id;
		$date = new Zend_Date();
		$data['modified_date'] 		= $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
		$data['metadata'] 			= json_encode($data['metadata']);
	
		$parentId = $data['parent_id'];
		$preOrder = $data['pre_order'];
		unset($data['parent_id']);
    	unset($data['pre_order']);
    	unset($data['order']);
    	unset($data['created_date']);
    	unset($data['created_user']);
    	unset($data['created_user_id']);
    	unset($data['modified_user']);
    	unset($data['hits']);
    	unset($data['id']);

		if($id > 0){
			// Update Parent
			$this->moveNode($id, 'right', $parentId);
			// Update Order
			if($preOrder != null){
				$orderValues = json_decode($orderValues, true);
				foreach ($orderValues as $orderValue){
					$this->moveNode($orderValue['id'], 'right', $parentId);
				}
			}
			// Update Status
			$this->updateBranch(array('status' => $data['status']), $id);
			// Update category
			$this->update($data, $this->_db->quoteInto('id = ?', $id));
		}
	}
	
	public function createItem($data){
		$user = Zendvn_Factory::getUser();
		$data['modified_user_id'] 	= $data['created_user_id'] = $user->id;
		$date = new Zend_Date();
		$data['modified_date'] 		= $data['created_date'] = $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
		$data['metadata'] 			= json_encode($data['metadata']);
	
		$parentId = $data['parent_id'];
		$preOrder = $data['pre_order'];
		unset($data['parent_id']);
		unset($data['pre_order']);
		unset($data['order']);
		unset($data['created_user']);
		unset($data['modified_user']);
		unset($data['id']);
	
		// Update Parent
		$id = $this->insertNode($data, 'right', $parentId);
		if($id > 0){
			// Update Order
			if($preOrder != null){
				$orderValues = json_decode($orderValues, true);
				foreach ($orderValues as $orderValue){
					$this->moveNode($orderValue['id'], 'right', $parentId);
				}
			}
			// Update Status
			$this->updateBranch(array('status' => $data['status']), $id);
		}
		return $id;
	}

	public function copyItem($id){
		$user = Zendvn_Factory::getUser();
		$item = $this->find($id)->current();
		if($item){
			$data = $item->toArray();
			unset($data['id']);
			$data['title'] = $this->copyTitle($data['title']);
			$data['alias'] = $this->copyAlias($data['alias']);
			if($data['image'])$data['image'] = $this->copyImage($data['image']);
			// Update create and modify
			$data['created_user_id'] = $data['modified_user_id'] = $user->id;
			$date = new Zend_Date();
			$data['created_date'] = $data['modified_date'] = $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
			// Reset Hits
			$data['hits'] = 0;
			$id = $this->insertNode($data, 'after', $id);
			return $id;
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
		if(count($items) > 0){
			foreach ($items as $item){
				$this->updateBranch(array('status' => $status), $item);
			}
		}
	}

	public function updateImage($imageName){
		$imagePath = PUBLISH_PATH . '/modules/contents/images/';
		$extension = pathinfo($imageName, PATHINFO_EXTENSION);
		$filename = pathinfo($imageName, PATHINFO_FILENAME);
		$imageName = $this->generateRandomString(10) . '_' .  $this->createAlias($filename) . '.' . $extension;
		$filterRename = new Zend_Filter_File_Rename(array('target' => $imagePath . $imageName, 'overwrite' => true));
		$filterRename->filter($imagePath . $filename . '.' . $extension);
		$image = Wideimage::load($imagePath . $imageName);
		$image = $image->resize(130, 130, 'outside');
		$image = $image->crop('center', 'center', 130, 130);
		$image->saveToFile($imagePath . 'thumbnails/' . $imageName);
		return $imageName;
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
	private function copyAlias($alias){
		$matches = array();
		preg_match_all('/' . preg_quote('-', '/') . '(\d+)/i', $alias, $matches);
		if($matches[1] != null){
			$alias = str_replace($matches[0][0],	'-' . ((integer)$matches[1][0] + 1), $alias);
		}else{
			$alias = $alias . '-1';
		}
		return $alias;
	}
	private function copyImage($image){
		$realName = explode("_", $image);
		$realName = str_replace($realName[0], '', $image);
		$newImage = $this->generateRandomString(10) . '_' .  $this->createAlias(pathinfo($realName, PATHINFO_FILENAME)) . '.' . pathinfo($realName, PATHINFO_EXTENSION);
		copy(PUBLISH_PATH . '/modules/contents/images/' . $image, PUBLISH_PATH . '/modules/contents/images/' . $newImage);
		copy(PUBLISH_PATH . '/modules/contents/images/thumbnails/' . $image, PUBLISH_PATH . '/modules/contents/images/thumbnails/' . $newImage);
		return $newImage;
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