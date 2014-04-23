<?php
class Zend_View_Helper_IsAllow extends Zend_View_Helper_Abstract
{

    public function isAllow($resource, $privilege)
    {
    	$role = Zendvn_Factory::getUser();
    	$acl = Zendvn_Factory::getAcl();  	
		return $acl->isAllowed($role, $resource, $privilege);
    }

}