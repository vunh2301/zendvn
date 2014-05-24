<?php
class Menus_Model_DbTable_Row_Menu extends Zend_Db_Table_Row_Abstract
{

	public function getModuleType(){
		// Get Menu Type
		$tblExtension = new Extensions_Model_DbTable_Extension();
		$rowExtension = $tblExtension->find($this->module_id)->current();
		$moduleTitle = $rowExtension->title;
		$module = $rowExtension->name;
		$controller = $this->controller;
		$paramsPath = APPLICATION_PATH . "/modules/$module/views/scripts/$controller/params.xml";
		if(file_exists($paramsPath)){
			$paramConfig = new Zend_Config_Xml($paramsPath, null, array('skipExtends' => true,'allowModifications' => true));
			$controllerTitle = $paramConfig->title;
		}
		return $moduleTitle . ' &raquo; ' . $controllerTitle;
	}
	
	public function getGroupAccess()
	{
		$acl = Zendvn_Factory::getAcl();
		$roles = $acl->getRoles();
		$groups = array();
		$all = true;
		foreach($roles as $role){
			if($acl->isAllowed($role,'menus.menuitems.' . $this->id, 'access')){
				$groups[str_replace('group_', '', $role)] = $acl->getRole($role)->getRoleName();
			}else{
				$all = false;
			}
		}
		if(true === $all) {
			return '*';
		}
		return $groups;
	}
	
	public function hasRule()
	{
		$acl = Zendvn_Factory::getAcl();
		return $acl->hasRule('contents.categories.' . $this->id);
	}
}