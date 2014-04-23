<?php
class Zend_View_Helper_IsAllow extends Zend_View_Helper_Abstract
{

    public function isAllow($resource, $privilege)
    {
    	$acl = Zendvn_Factory::getAcl();  	
		return $acl->isAllowed('current-user', $resource, $privilege);
    }

}