<?php 
class Zendvn_Db_Table_User extends Zendvn_Db_Table_Abstract{
	protected $_name	= 'users';
	protected $_primary = 'id';
	
	public function getItem($id){
		if (!is_integer($id)) {
			throw new Exception('Invalid argument: $id must be a integer');
		}
		if(null === ($item = $this->fetchRow($this->select()->where("id = ?", $id)))){
			return null;
		}
		return $item;
	}
	
	public function getGroups($id){
		$tblGroup = new Zendvn_Db_Table_Group();
		return $tblGroup->fetchAll($tblGroup->select()->setIntegrityCheck(false)
				->from('groups')
				->joinLeft(array('ug' => 'user_group'), 'groups.id = ug.group_id', array('user_id'))
				->where('ug.user_id = ?', $id)
				->group('groups.id')
		);
	}
}
?>