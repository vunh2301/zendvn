<?php 
class Zendvn_Db_Table_User extends Zendvn_Db_Table_Abstract{
	protected $_name	= 'users';
	protected $_primary = 'id';
	
	public function getItem($id){
		if (!is_integer($id)) {
			throw new Exception('Invalid argument: $id must be a integer');
		}
		if(null === ($item = $this->find($id)->current())){
			return null;
		}
		return $item;
	}
}
?>