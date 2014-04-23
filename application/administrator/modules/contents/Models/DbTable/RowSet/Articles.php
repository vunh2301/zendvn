<?php
class Contents_Model_DbTable_Rowset_Articles extends Zend_Db_Table_Rowset_Abstract
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
			$access = $acl->getGroups('site:module:contents.articles.access.' . $row['id'], 'access');
			if(in_array($groupId, $access)){
				$data[] = $row;
			}
		}
		$this->_data = $data;
		$this->_count = count($this->_data);
		return $this;
	}
}