<?php

class Zendvn_Acl_Role implements Zend_Acl_Role_Interface
{

    protected $_roleId;
    
    protected $_roleName;

    public function __construct($roleId, $roleName)
    {
        $this->_roleId = (string) $roleId;
        $this->_roleName = (string) $roleName;
    }

    public function getRoleId()
    {
        return $this->_roleId;
    }
    
    public function getRoleName()
    {
    	return $this->_roleName;
    }

    public function __toString()
    {
        return $this->getRoleId();
    }
}
