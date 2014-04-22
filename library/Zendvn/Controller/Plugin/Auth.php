<?php 
class Zendvn_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract{
	public function routeShutdown(Zend_Controller_Request_Abstract $request){
		$user 		= Zendvn_Factory::getUser();
		$acl 		= Zendvn_Factory::getAcl();	
		$resource 	= $request->getModuleName();
		if(Zendvn_Factory::getLocation() == 'admin'){
			if($acl->isAllowed($user, $resource, 'admin') === false){
				if($user->isGuest() === true){
					$request->setModuleName('admin')->setControllerName('login')->setActionName('index');
				}else{
					$request->setModuleName('admin')->setControllerName('error')->setActionName('error');
				}
			}
		}else{
			if($request->getParam('pid') > 0)
				$resource = 'menus.menuitems.' . $request->getParam('pid');
			
			if($acl->isAllowed($user, $resource, 'access') === false){
				if($user->isGuest() === true){
					$request->setModuleName('user')->setControllerName('login')->setActionName('index');
				}else{
					$request->setModuleName('index')->setControllerName('error')->setActionName('error');
				}
			}	
		}
		
	}
}
?>