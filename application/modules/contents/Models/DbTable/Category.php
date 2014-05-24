<?php
class Contents_Model_DbTable_Category extends Zendvn_Db_Table_NestedSet
{
	protected $_name		= 'categories';
	
	protected $_rowClass 	= 'Contents_Model_DbTable_Row_Category';
	
	protected $_dependentTables = array('Contents_Model_DbTable_Article');
	
	private $_globalTimezone;
	
	public function init(){
		$appConfig = Zendvn_Factory::getAppConfig();
		$this->_globalTimezone = $appConfig['site']['timezone'];
	}
	
	public function getCategory($id){
		$item = $this->fetchRow($this->select()->setIntegrityCheck(false)
				->from('categories')
				->where('categories.id = ?', $id)
				->joinLeft('users', 'categories.created_user_id = users.id', array('created_user' => 'users.real_name'))
		);
		
		$item->metadata = json_decode($item->metadata, true);
		return $item;
	}
	
}
?>