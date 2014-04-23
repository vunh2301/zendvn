<?php 
// Object is Current Users
class Zendvn_User extends Zendvn_Object_Abstract{

	private static $_instance = null;
	
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
	
		return self::$_instance;
	}
	
	public function init()
	{
		$id = 0;
		$auth = Zend_Auth::getInstance();
		$tblUser = new Zendvn_Db_Table_User();
		if(false !== $auth->hasIdentity() && ($id = (int)$auth->getStorage()->read()->id) > 0 && null !== ($user = $tblUser->getItem($id))){
			$this->sessionId = session_id();
			$this->isGuest = false;
			$this->setProperties($user->toArray());
		}else{
			$this->sessionId = session_id();
			$this->id = 0;
			$this->isGuest = true;
			$this->realName = "Guest";
		}
	}
	
	public function isGuest(){
		return $this->get('isGuest') === true;
	}
	
	public function getParams(){
		return $this->_properties['params'];
	}
	
	public function getGroups(){
		if(false === isset($this->_properties['groups'])){
			$tblUser = new Zendvn_Db_Table_User();
			$this->_properties['groups'] = $tblUser->getGroups($this->id);
		}
		return $this->_properties['groups'];
	}
	
	public function setParams($params){
		$this->_properties['params'] = json_decode($params, true);
	}
	
	public function isAllowed($resource = null, $privilege = null){
		$acl = Zendvn_Factory::getAcl();
		return $acl->isAllowed($this, $resource, $privilege);
	}
}
