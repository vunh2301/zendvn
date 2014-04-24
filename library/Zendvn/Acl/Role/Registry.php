<?php
class Zendvn_Acl_Role_Registry extends Zend_Acl_Role_Registry
{
    public function moveRole($role, $parents){
    	if($this->has($role)){
    		if($role instanceof Zend_Acl_Role_Interface){
    			$roleId = $role->getRoleId();
    		}else if (is_string($role)) {
    			$roleId = $role;
    			$role = $this->get($role);
    		}
    	}
    	
    	//old parent
    	$oldParents = $this->_roles[$roleId]['parents'];
    	$oldParentId = array();
    	foreach ($oldParents as $oldParentId => $oldParent){
    		$oldParentId = $oldParent->getRoleId();
    		unset($this->_roles[$oldParentId]['children'][$roleId]);
    	}
    	
    	if(is_array($parents)){
    		unset($this->_roles[$roleId]['parents']);
    		foreach ($parents as $parent){
    			if($this->has($parent)){
    				if($parent instanceof Zend_Acl_Role_Interface){
    					$roleParentId = $parent->getRoleId();
    				}else if (is_string($parent)) {
    					$roleParentId = $parent;
    					$parent = $this->get($parent);
    				}
    				 
    				$this->_roles[$roleParentId]['children'][$roleId] = $role;
    				$this->_roles[$roleId]['parents'][$roleParentId] = $parent;
    			}
    		}
    	}else{
    		if($this->has($parents)){
    			if($parents instanceof Zend_Acl_Role_Interface){
    				$roleParentId = $parents->getRoleId();
    			}else if (is_string($parents)) {
    				$roleParentId = $parents;
    				$parents = $this->get($parents);
    			}
    			
    			$this->_roles[$roleParentId]['children'][$roleId] = $role;
    			unset($this->_roles[$roleId]['parents']);
    			$this->_roles[$roleId]['parents'][$roleParentId] = $parents;
    		}
    	}
    	return $this;
	}
}
