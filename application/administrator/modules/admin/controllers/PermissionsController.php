<?php
class PermissionsController extends Zend_Controller_Action
{

    public function init()
    {
    	
    }

    public function indexAction()
    {
    	$acl = Zendvn_Factory::getAcl();
		$tblExtension = new Extensions_Model_DbTable_Extension();
		$tblResource = new Zendvn_Db_Table_AclResource();
		$modules = $tblExtension->fetchAll($tblExtension->select()->setIntegrityCheck(false)
				->from(array('ext' => 'extensions'),null)
				->where('ext.type = ?', 'module')
				->where('ext.name != ?', 'admin')
				->joinLeft(array('res' => 'acl_resources'), 'res.name = ext.name', array('title' => 'res.title', 'resource' => 'res.name'))
				->where('res.id > ?', 0)
		);
   		 foreach($modules as $module){
			if($this->_request->getParam($module->resource, null) !== null)
				$tblResource->updatePrivileges($module->resource, $this->_request->getParam($module->resource, null));
		}
		$this->view->modules = $modules;
    }

}