<?php 
class Zendvn_Factory{
	public function getAppConfig(){
		$front = Zend_Controller_Front::getInstance();
		return $front->getParam('bootstrap')->getOptions();
	}
	
	// Get Cache
	public function getCache($name){
		$manager = Zend_Registry::get('Zendvn_Cache');
		$dbCache = array(
				'frontend' => array(
						'name' => 'Core',
						'options' => array(
								'lifetime' => 7200,
								'automatic_serialization' => true
						)
				),
				'backend' => array(
						'name' => 'File',
						'options' => array(
								'cache_dir' => APPLICATION_PATH . '/cache'
						)
				)
		);
		if ($manager->hasCache($name)) {
		    return $manager->getCache($name);
		} else {
		    return $manager->setCacheTemplate($name, $dbCache)->getCache($name);
		}
	}
	
	// Get Location
	public function getLocation(){
		return Zend_Registry::get('Zendvn_Location');
	}
	
	// Get Current User
	public static function getUser(){
		return Zendvn_User::getInstance();
	}

	public static function getAcl(){
		$cache = Zendvn_Factory::getCache('Cms');
		if (!($acl = $cache->load('Acl'))) {
			$acl = Zendvn_Acl::getInstance();
			$cache->save($acl);
		}
		return $acl;
	}
	
	public static function getMenu(){
		$cache = Zendvn_Factory::getCache('Cms');
		if (!($menu = $cache->load('Menu'))) {
			$menu = Zendvn_Menu::getInstance();
			$cache->save($menu);
		}
		return $menu;
	}
	
	public function getWidgets(){
		if(!Zend_Registry::isRegistered('Zendvn_widgets')){
			$tblWidget = new Zendvn_Db_Table_Widget();
			$widget = $tblWidget->getItems(Zend_Registry::get('Zendvn_Location'));
			Zend_Registry::set('Zendvn_widgets', $widget);
		}
		return Zend_Registry::get('Zendvn_widgets');
	}
	
	public function getNavigation($menuTypeId = 0){
		if(Zend_Registry::get('Zendvn_Location') == 'admin'){
			if(Zend_Registry::isRegistered('Admin_Navigation_Container') == false){
				$container 		= new Zend_Navigation();
				$tblExtension 	= new Zendvn_Db_Table_Extension();
				$extensions 	= $tblExtension->getModules();
				if($extensions != null){
					foreach ($extensions as $extension){
						$moduleInfoPath = APPLICATION_PATH . '/administrator/modules/' . $extension->name.'/info';
						if (($configModuleInfo = Zendvn_Config::factory($moduleInfoPath, null, array('skipExtends' => true,'allowModifications' => true))) !== null) {
							if($configModuleInfo->administration->nav != null && $configModuleInfo->administration->nav->position == 'root'){
								unset($configModuleInfo->administration->nav->position);
								foreach ($configModuleInfo->administration->nav as $menu){
									$container->addPage($menu);
								}
							}
						}
					}
				}
					
				if($extensions != null){
					foreach ($extensions as $extension){
						$moduleInfoPath = APPLICATION_PATH . '/administrator/modules/' . $extension->name.'/info';
						if (($configModuleInfo = Zendvn_Config::factory($moduleInfoPath, null, array('skipExtends' => true,'allowModifications' => true))) !== null) {
							if($configModuleInfo->administration->nav != null && $configModuleInfo->administration->nav->position != 'root'){
								$parentPage = $container->findBy('id', $configModuleInfo->administration->nav->position);
								unset($configModuleInfo->administration->nav->position);
								if($parentPage != null){
									foreach ($configModuleInfo->administration->nav as $menu){
										$parentPage->addPage($menu);
									}
								}
							}
						}
					}
				}
				Zend_Registry::set('Admin_Navigation_Container', $container);
			}else{
				$container = Zend_Registry::get('Admin_Navigation_Container');
			}
			return $container;
		}else{
			$menuTypeId = $menuTypeId > 0 ? $menuTypeId : $this->getMenu()->getHomeMenu()->menu_location;
			$menus 		= $this->getMenu()->getMenus();
			if($menus->count() > 0){
				foreach ($menus as $menu){
					if($menu->menu_type_id == $menuTypeId)
						$arrMenus[] = $menu;
				}
			}
			return $this->toNavigation($arrMenus);
		}
	}

	public function addBreadcrumb($breadcrumb){
		Zend_Registry::set('Admin_Navigation_Breadcrumbs', $breadcrumb);
	}
	
	public function getBreadcrumb(){
		if(Zend_Registry::isRegistered('Admin_Navigation_Breadcrumbs')){
			return Zend_Registry::get('Admin_Navigation_Breadcrumbs');
		}else{
			return array();
		}
	}
}
