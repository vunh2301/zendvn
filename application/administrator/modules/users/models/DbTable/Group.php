<?php

class Users_Model_DbTable_Group extends Zendvn_Db_Table_NestedSet
{
	protected $_name	= 'groups';
	
	protected $_dependentTables = array('Users_Model_DbTable_UserGroup', 'Zendvn_Db_Table_AclPrivilege');
}

?>