<?php
class Zendvn_Acl_Resource implements Zend_Acl_Resource_Interface
{

    protected $_resourceId;
    protected $_id;

    public function __construct($resourceId, $id = 0)
    {
        $this->_resourceId = (string) $resourceId;
        $this->_id = (int) $id;
    }

    public function getResourceId()
    {
        return $this->_resourceId;
    }

    public function getId()
    {
    	return $this->_id;
    }
    
    public function setId($id)
    {
    	$this->_id = (int) $id;
    	return $this;
    }

    public function __toString()
    {
        return $this->getResourceId();
    }
}
