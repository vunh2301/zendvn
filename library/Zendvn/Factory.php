<?php 
class Zendvn_Factory{
	public function getAppConfig(){
		$front = Zend_Controller_Front::getInstance();
		return $front->getParam('bootstrap')->getOptions();
	}
	
	// Get Location
	public function getLocation(){
		return Zend_Registry::get('Zenvn_Location');
	}
	
	// Get Current User
	public static function getUser(){
		return Zendvn_User::getInstance();
	}

	public static function getAcl(){
		return Zendvn_Acl::getInstance();
	}
	
	public static function getMenu(){
		return Zendvn_Menu::getInstance();
	}
	
	public function getWidgets(){
		if(!Zend_Registry::isRegistered('WIDGETS')){
			$tblWidget = new Zendvn_Db_Table_Widget();
			$widget = $tblWidget->getItems(Zend_Registry::get('Zenvn_Location'));
			Zend_Registry::set('WIDGETS', $widget);
		}
		return Zend_Registry::get('WIDGETS');
	}
	
	public function getNavigation($menuTypeId = 0){
		if(Zend_Registry::get('Zenvn_Location') == 'admin'){
			if(Zend_Registry::isRegistered('Admin_Navigation_Container') == false){
				$container 		= new Zend_Navigation();
				$tblExtension 	= new Zendvn_Db_Table_Extension();
				$extensions 	= $tblExtension->getModules();
				if($extensions != null){
					foreach ($extensions as $extension){
						$moduleInfoPath = APPLICATION_PATH . '/administrator/modules/' . $extension->name.'/info.xml';
						if (file_exists($moduleInfoPath)) {
							$configModuleInfo = new Zend_Config_Xml($moduleInfoPath, null, array('skipExtends' => true,'allowModifications' => true));
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
						$moduleInfoPath = APPLICATION_PATH . '/administrator/modules/' . $extension->name.'/info.xml';
						if (file_exists($moduleInfoPath)) {
							$configModuleInfo = new Zend_Config_Xml($moduleInfoPath, null, array('skipExtends' => true,'allowModifications' => true));
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
			$menuTypeId = $menuTypeId > 0 ? $menuTypeId : Zendvn_Menu::getInstance()->getHomeMenu()->menu_location;
			$menus 		= Zendvn_Menu::getInstance()->getMenus();
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
