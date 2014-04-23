<?php

class Users_Model_DbTable_UserGroup extends Zendvn_Db_Table_Abstract
{
	protected $_name	= 'user_group';

	protected $_referenceMap    = array(
        'User' => array(
            'columns'           => array('user_id'),
            'refTableClass'     => 'Users_Model_DbTable_User',
            'refColumns'        => array('id'),
			'onDelete'          => self::CASCADE,
			'onUpdate'          => self::RESTRICT
        ),
        'Group' => array(
            'columns'           => array('group_id'),
            'refTableClass'     => 'Users_Model_DbTable_Group',
            'refColumns'        => array('id'),
			'onDelete'          => self::CASCADE,
			'onUpdate'          => self::RESTRICT
        )
    );
}

?>