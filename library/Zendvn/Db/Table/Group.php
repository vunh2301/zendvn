<?php 
class Zendvn_Db_Table_Group extends Zendvn_Db_Table_Abstract{
	protected $_name	= 'groups';
	protected $_primary = 'id';
	
	public function getItems(){
		return $this->fetchAll($this->select()->order('lft'));
	}
}
?>