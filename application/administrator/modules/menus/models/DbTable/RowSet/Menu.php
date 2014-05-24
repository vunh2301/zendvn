<?php
class Menus_Model_DbTable_Rowset_Menu extends Zend_Db_Table_Rowset_Abstract
{
	public function search($searchword = null, $field = null){
		if($searchword != null && $field != null){
			$data = array();
			
			foreach ($this as $row){
				if (stripos(strtolower($row->$field), strtolower($searchword)) !== false){
					$data[] = $row->toArray();
					$rows[] = $row;
				}
			}
			$this->_data = $data;
			$this->_rows = $rows;
			$this->_count = count($this->_data);
				
		}
		return $this;
	}
	
	public function match($searchword = null, $field = null){
		if($searchword == '*')return $this;
		if($searchword != null && $field != null){
			$data = array();
			foreach ($this as $row){
				if ($row->$field == $searchword){
					$data[] = $row->toArray();
					$rows[] = $row;
				}
			}
			$this->_data = $data;
			$this->_rows = $rows;
			$this->_count = count($this->_data);
	
		}
		return $this;
	}
	
	public function matchGroup($groupId){
		if($groupId == 0)return $this;
		$tblGroups = new Users_Model_DbTable_Group();
		$acl = Library_Factory::getAcl();
		$data = array();
		foreach ($this as $row){
			$rowsetGroups = $row->getGroupAccess();
			$access = array();
			if($rowsetGroups->count() > 0){
				foreach($rowsetGroups as $rowGroup)$access[] = $rowGroup->id;
			}
			
			if(in_array($groupId, $access)){
				$data[] = $row->toArray();
				$rows[] = $row;
			}
		}

		$this->_data = $data;
		$this->_rows = $rows;
		$this->_count = count($this->_data);

		return $this;
	}
	
	public function recursive($parentId = 0){
		$this->_data = $this->_recursive($this->_data, $parentId);
		$this->_rows = array();
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
	
	public function nested($active = 0, $parentId = 0){
		if($active === 0){
			$id = $this->getTable()->getAutoIncrement();
			return '<li class="dd-item active" data-id="' . $id . '"><div class="dd-handle">Current Menu</div></li>' . $this->_nested($active, $parentId) .'';
		}
		$nested = $this->_nested($active, $parentId);
		return $nested;
	}
	
	private function _nested($active, $parentId = 0){
		$outData = '';
		$data = clone $this;
		foreach($data as $item) {
			if($item->parent_id == $parentId) {
				$subItems = $this->_nested($active, $item->id);
				$outData .= '<li class="dd-item' . ($active == $item->id ? ' active' : '') . '" data-id="' . $item->id . '">';
				$outData .= '<div class="dd-handle">' . $item->title . '</div>';
				if($subItems != null){
					$outData .= '<ol class="dd-list">';
					$outData .= $subItems;
					$outData .= '</ol>';
				}
				$outData .= '</li>';
			}
		}
		return $outData;
	}	

	public function nestedFormParams($params, $active = 0){
		return $this->_nestedFormParams($params, $active);
	}
	
	private function _nestedFormParams($params, $active){
		$outData = '';
		foreach($params as $item) {
				$row = $this->getTable()->find($item['id'])->current();
				$subItems = null;
				if(isset($item['children'])){
					$subItems = $this->_nestedFormParams($item['children'], $active);
				}
				if($row){
					$outData .= '<li class="dd-item' . ($active == $row->id ? ' active' : '') . '" data-id="' . $row->id . '">';
					$outData .= '<div class="dd-handle">' . $row->title . '</div>';
					if($subItems != null){
						$outData .= '<ol class="dd-list">';
						$outData .= $subItems;
						$outData .= '</ol>';
					}
					$outData .= '</li>';
				}else{
					$outData .= '<li class="dd-item active" data-id="' . $item['id'] . '">';
					$outData .= '<div class="dd-handle">Current Menu</div>';
					if($subItems != null){
						$outData .= '<ol class="dd-list">';
						$outData .= $subItems;
						$outData .= '</ol>';
					}
					$outData .= '</li>';
				}
		}
		return $outData;
	}
}

?>