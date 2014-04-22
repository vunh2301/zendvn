<?php
class Zendvn_Db_Table_AclResource extends Zendvn_Db_Table_NestedSet
{
	protected $_name	= 'acl_resources';
	protected $_primary = 'id';
	protected $_dependentTables = array('Zendvn_Db_Table_AclPrivilege');

	public function getItems(){
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('res'=>'acl_resources'), array('id', 'parent_id', 'level', 'name'))
		->joinLeft(array('pri' => 'acl_privileges'), 'pri.resource_id = res.id', array('privilege', 'assert' , 'group_id'))
		->order('lft');
		return $this->fetchAll($select);
	}
	
	public function updatePrivileges($resource, $data){
		$acl = Zendvn_Factory::getAcl();
		if(is_string($resource)){
			$resourceId = $acl->get($resource)->getId();
		}
		if($resourceId > 0){
			$tblPrivilege = new Zendvn_Db_Table_AclPrivilege();
			$rowsetPrivileges = $tblPrivilege->fetchAll($tblPrivilege->select()->where('resource_id = ?', $resourceId));
			if($rowsetPrivileges->count() > 0){
				foreach($rowsetPrivileges as $nodeDelete){
					$remove = 'remove' . ucfirst($nodeDelete->assert);
					$acl->$remove('group_' . $nodeDelete->group_id, $resource, $nodeDelete->privilege);
					$nodeDelete->delete();
				}
			}
			foreach ($data as $key => $assert){
				if($assert != 'inhereit' && in_array($assert, array('allow','deny'))){
					$keyPath 	= explode("_", $key);
					$groupId = $keyPath[0];
					$pri = $keyPath[1];
					if($groupId > 0 && $pri != null){
						$privilegeInsert = array(
								'privilege' => $pri,
								'assert'	=> $assert,
								'group_id'	=> $groupId,
								'resource_id' => $resourceId
						);
						$tblPrivilege->insert($privilegeInsert);
						$acl->$assert('group_' . $groupId, $resource, $pri);
					}
				}
			}
		}
	
	}
	
	public function addResource($resource, $title, $parentResource = null){
		$acl = Zendvn_Factory::getAcl();
		if($parentResource === null){
			$namePath 	= explode(".", $resource);
			$parentResource = $namePath[0];
		}
		if(is_string($parentResource)){
			$parentNode = $acl->get($parentResource);
			$parentId = $parentNode->getId();
		}
		$data = array(
				'name' 	=> $resource,
				'title'	=> $title
		);
		if($parentId > 0){
			$resourceId = $this->insertNode($data, 'right', $parentId);
			$acl->add(new Zendvn_Acl_Resource($resource, $resourceId), $parentResource);
		}
		return $resourceId;
	}
	
	public function moveResource($resource, $parentResource){
		$acl = Zendvn_Factory::getAcl();
		$node = $acl->get($resource);
		$nodeId = $node->getId();
		if($parentResource === null){
			$namePath 	= explode(".", $resource);
			$parentResource = $namePath[0];
		}
		if(is_string($parentResource)){
			$parentNode = $acl->get($parentResource);
			$parentId = $parentNode->getId();
		}
		if($parentId > 0 && $nodeId > 0){
			$resourceId = $this->moveNode($nodeId, 'right', $parentId);
			$acl->moveResource($resourceId, $parentId);
		}
	}
}