<?php 
class Zendvn_Db_Table_Template extends Zendvn_Db_Table_Abstract{
	protected $_name	= 'templates';
	protected $_primary = 'id';
	
	public function getDefaultTemplate(){
		$location = Zendvn_Factory::getLocation();
		return $this->fetchRow($this->select()->setIntegrityCheck(false)
					->from('templates')
					->joinLeft('extensions', 'extensions.id = templates.extension_id', array('template' => 'extensions.name'))
					->where('templates.isDefault = ?', 1)->where('extensions.location = ?', $location));
	}
}
?>