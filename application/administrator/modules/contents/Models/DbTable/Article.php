<?php
require_once APPLICATION_PATH . '/library/Wideimage/Wideimage.php';
class Contents_Model_DbTable_Article extends Zendvn_Db_Table_Abstract
{
	protected $_name		= 'articles';
	
	protected $_rowsetClass = 'Contents_Model_DbTable_Rowset_Articles';
	
	protected $_rowClass 	= 'Contents_Model_DbTable_Row_Article';
	
	private $_globalTimezone;
	
	private $_categories;

	public function init(){
		$appConfig = Zendvn_Factory::getAppConfig();
		$this->_globalTimezone = $appConfig['site']['timezone'];
	}
	
	public function deleteItem($id){
		$item = $this->find($id)->current();
		$image = $item->image;
		$item->delete();
		// Remove permission
		$acl = Zendvn_Factory::getAcl();
		$resource_id = $acl->get('contents.articles.' . $id)->getId();
		$tblResource = new Zendvn_Db_Table_AclResource();
		$tblResource->removeNode($resource_id);
		// Delete Image
		if(is_file(PUBLISH_PATH . '/modules/contents/images/' . $image))unlink(PUBLISH_PATH . '/modules/contents/images/' . $image);
		if(is_file(PUBLISH_PATH . '/modules/contents/images/thumbnails/' . $image))unlink(PUBLISH_PATH . '/modules/contents/images/thumbnails/' . $image);
		
	}
	
	public function getItems($filter){
		$select = $this->select()
		->from('articles')
		->setIntegrityCheck(false)
		->joinLeft('categories', 'articles.category_id = categories.id', array('category' => 'categories.title'))
		->group('articles.id');

		// Filter Search
		if(isset($filter['search']) && null != $filter['search'])
			$select->where('articles.title LIKE("%' . $filter['search'] . '%")');

		// Filter Category
		if(isset($filter['category']) && $filter['category'] != '*')
			$select->where('articles.category_id = ?', $filter['category']);
		
		// Filter Status
		if(isset($filter['status']) && $filter['status'] != '*')
			$select->where('articles.status = ?', $filter['status']);
		
		// Filter Featured
		if(isset($filter['featured']) && $filter['featured'] != '*')
			$select->where('articles.featured = ?', (int)$filter['featured']);
		
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
		$item = $this->fetchRow($this->select()->setIntegrityCheck(false)
				->from('articles')
				->where('articles.id = ?', $id)
				->joinLeft(array('cusers' => 'users'), 'articles.created_user_id = cusers.id', array('created_user' => 'cusers.real_name'))
				->joinLeft(array('musers' => 'users'), 'articles.modified_user_id = musers.id', array('modified_user' => 'musers.real_name'))
				->group('articles.id')
		);
			
		$item->metadata = json_decode($item->metadata, true);
		return $item;
	}
	
	public function updateItem($id, $data){
		$user = Zendvn_Factory::getUser();
		$data['modified_user_id'] = $user->id;
		$date = new Zend_Date();
		$data['modified_date'] = $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
		if($data['publish_date_start'] != null){
			$date = new Zend_Date($data['publish_date_start']);
			$data['publish_date_start'] = $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
		}else{
			$data['publish_date_start'] = '0000-00-00 00:00:00';
		}
		
		if($data['publish_date_end'] != null){
			$date = new Zend_Date($data['publish_date_end']);
			$data['publish_date_end'] = $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
		}else{
			$data['publish_date_end'] = '0000-00-00 00:00:00';
		}
		
		$data['metadata'] = json_encode($data['metadata']);
		
		// Get Description
		if( strpos($data['text'], "<hr id=\"content-readmore\" />") !== false){
			$fulltext = explode("<hr id=\"content-readmore\" />", $data['text']);
			$data['description'] = $fulltext[0];
		}

		unset($data['created_date']);
		unset($data['created_user_id']);
		if($id > 0){
			$article = $this->find($id)->current()->setFromArray($data);
			return $article->save();
		}
	}

	public function createItem($data){
		$user = Zendvn_Factory::getUser();
		$data['created_user_id'] = $data['modified_user_id'] = $user->id;		
		$date = new Zend_Date();
		$data['created_date'] = $data['modified_date'] = $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
		
		if($data['publish_date_start']){
			$date = new Zend_Date($data['publish_date_start']);
		}else{
			$date = new Zend_Date();
		}
		$data['publish_date_start'] = $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
		
		if($data['publish_date_end']){
			$date = new Zend_Date($data['publish_date_end']);
			$data['publish_date_end'] = $date->setTimezone($this->_globalTimezone)->toString('YYYY-MM-dd HH:mm:ss');
		}else{
			$data['publish_date_end'] = '0000-00-00 00:00:00';
		}
	
		$data['metadata'] = json_encode($data['metadata']);
		
		// Get Description
		if( strpos($data['text'], "<hr id=\"content-readmore\" />") !== false){
			$fulltext = explode("<hr id=\"content-readmore\" />", $data['text']);
			$data['description'] = $fulltext[0];
		}

		$article = $this->createRow($data);
		return $article->save();
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
			$newItem = $this->createRow($data);
			return $newItem->save();
		}
	}
	
	public function getCategories($type = 'list'){
		$tblCategory = new Contents_Model_DbTable_Category();
		$items = $tblCategory->fetchAll($tblCategory->select()->where('lft > 0')->order('lft'));
		
		if($type == 'rowset') return $items;
		if($items->count() > 0){
			foreach ($items as $item) $list[$item->id] = str_repeat('|— ', $item->level - 1) . ' ' . $item->title;
			return $list;
		}
		return array();
	}
	
	public function updateStatus(array $items, $status = 'publish'){
		$rows = $this->find($items);
		if($rows->count() > 0){
			foreach ($rows as $row){
				$row->status = $status;
				$row->save();
			}
		}
	}
	
	public function updateFeatured(array $items, $featured = true){
		$rows = $this->find($items);
		if($rows->count() > 0){
			foreach ($rows as $row){
				$row->featured = $featured;
				$row->save();
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