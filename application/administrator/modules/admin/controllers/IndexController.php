<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
    	
    }

    public function indexAction()
    {
		$acl = Zendvn_Factory::getAcl();
		Zend_Debug::dump($acl->getResources()); 
    }

}