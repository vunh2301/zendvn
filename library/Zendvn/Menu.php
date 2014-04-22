<?php 
class Zendvn_Menu {
	private $_menus;
	
	private static $_instance = null;

	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
	
		return self::$_instance;
	}
	
	public function getMenus(){
		if($this->_menus == null){
			$tblMenu = new Zendvn_Db_Table_Menu();
			$this->_menus = $tblMenu->getItems();
			$this->createLongAlias($this->_menus);
		}
		return $this->_menus;
	}
	
	public function getMenuByMenuType($id){
		$menus = $this->getMenus();
		$_menus = array();
		foreach ($menus as $menu){
			if($menu->menu_type_id == $id)$_menus[] = $menu;
		}
		return $this->$_menus;
	}
	
	public function getHomeMenu(){
		$menus = $this->getMenus();
		foreach ($menus as $menu){
			if($menu->home)return $menu;
		}
		return null;
	}
	
	public function getMenu($id){
		$menus = $this->getMenus();
		foreach ($menus as $menu){
			if($menu->id == $id)return $menu;
		}
		return null;
	}
	
	public function createLongAlias($menus){
		$_menus = array();
		foreach ($menus as $menu){
			if($menu->parent_id > 1){
				$menu->alias = $_menus[$menu->parent_id] . '/' . $menu->alias;
			}
			$_menus[$menu->id] = $menu->alias;
		}
	}
}

?>