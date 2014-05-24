<?php
class Contents_Model_DbTable_Row_Article extends Zendvn_Db_Table_Row_Abstract
{
	private $_userTimezone;
	
	public function init()
	{
		$user = Zendvn_Factory::getUser();
		if($user->isGuest() === true){
			$appConfig = Zendvn_Factory::getAppConfig();
			$this->_userTimezone = $appConfig['site']['timezone'];
		}else{
			$this->_userTimezone  = $user->params['timezone'];
		}
	}
	
	public function __get($columnName)
	{
		$info = $this->_table->info();
		$metadata = $info['metadata'];
		$data = parent::__get($columnName);
		if(isset($metadata[$columnName]['DATA_TYPE']) && $metadata[$columnName]['DATA_TYPE'] == 'datetime'){
			if($data != '0000-00-00 00:00:00'){
				$date = new Zend_Date($data);
				$data = $date->setTimezone($this->_userTimezone)->toString('dd-MM-yyyy hh:mm a');
			}else{
				$data = '';
			}
		}
		return $data;
	}
	
	public function toArray()
	{
		$info = $this->_table->info();
		$metadata = $info['metadata'];
		$data = (array)$this->_data;
		$_data = array();
		foreach ($data as $columnName => $value){
			if(isset($metadata[$columnName]['DATA_TYPE']) && $metadata[$columnName]['DATA_TYPE'] == 'datetime'){
				if($value != '0000-00-00 00:00:00' && $value != null){
					$date = new Zend_Date($value);
					$_data[$columnName] = $date->setTimezone($this->_userTimezone)->toString('dd-MM-yyyy hh:mm a');
				}else{
					$_data[$columnName] = '';
				}
			}else{
				$_data[$columnName] = $value;
			}		
		}
		return $_data;
	}
	
	public function getGroupAccess()
	{
		$acl = Zendvn_Factory::getAcl();
		$roles = $acl->getRoles();
		$groups = array();
		$all = true;
		foreach($roles as $role){
			if($acl->isAllowed($role,'contents.articles.' . $this->id, 'access')){
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
		return $acl->hasRule('contents.articles.' . $this->id);
	}
}

?> 