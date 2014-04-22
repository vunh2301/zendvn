<?php 
class Zendvn_Db_Table_Extension extends Zendvn_Db_Table_Abstract{
	
	protected $_name	= 'extensions';
	
	protected $_primary = 'id';
	
	public function getModules(){
		return $this->fetchAll($this->select()->where('type = ?','module'));
	}
}
?>