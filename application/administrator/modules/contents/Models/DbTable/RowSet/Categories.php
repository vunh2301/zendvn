<?php
class Contents_Model_DbTable_Rowset_Categories extends Zend_Db_Table_Rowset_Abstract
{
	public function search($searchword = null, $field = null){
		if($searchword != null && $field != null){
			$data = array();
			foreach($this->_data as $pos => $row) {
				if (stripos(strtolower($row[$field]), strtolower($searchword)) !== false){
					$data[] = $row;
				}
			}
			$this->_data = $data;
			$this->_count = count($this->_data);
	
		}
		return $this;
	}
	

	public function match($searchword = null, $field = null){
		if($searchword == '*')return $this;
		if($searchword != null && $field != null){
			$data = array();
			foreach($this->_data as $pos => $row) {
				if ($row[$field] == $searchword){
					$data[] = $row;
				}
			}
			$this->_data = $data;
			$this->_count = count($this->_data);
	
		}
		return $this;
	}
	
	public function matchGroup($groupId){
		if($groupId == 0)return $this;
		$acl = Library_Factory::getAcl();
		$data = array();
		foreach($this->_data as $pos => $row){
			$access = $acl->getGroups('site:module:contents.categories.access.' . $row['id'], 'access');
			if(in_array($groupId, $access)){
				$data[] = $row;
			}
		}
		$this->_data = $data;
		$this->_count = count($this->_data);
		return $this;
	}
	
	public function recursive($parentId = 0){
		$this->_data = $this->_recursive($this->_data, $parentId);
		$this->_count = count($this->_data);
		return $this;
	}
	
	private function _recursive($data, $parentId = 0, $level = 0){
		$outData = array();
		foreach($data as $item) {
			if($item['parent_id'] == $parentId) {
				$item['recursive_level'] = $level;
				$subItems = $this->_recursive($data, $item['id'], $level + 1);
				$outData[] = $item;
				if($subItems != null){
					foreach ($subItems as $subItem){
						$outData[] = $subItem;
					}
				}
			}
		}
		return $outData;
	}
}