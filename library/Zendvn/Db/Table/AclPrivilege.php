<?php
class Zendvn_Db_Table_AclPrivilege extends Zend_Db_Table_Abstract
{
	protected $_name	= 'acl_privileges';
	protected $_primary = 'resource_id';
	protected $_referenceMap    = array(
		'AclResource' => array(
				'columns'           => array('resource_id'),
				'refTableClass'     => 'Zendvn_Db_Table_AclResource',
				'refColumns'        => array('id'),
				'onDelete'          => self::CASCADE,
				'onUpdate'          => self::RESTRICT
		),
		'Group' => array(
				'columns'           => array('group_id'),
				'refTableClass'     => 'Zendvn_Db_Table_Group',
				'refColumns'        => array('id'),
				'onDelete'          => self::CASCADE,
				'onUpdate'          => self::RESTRICT
		)
	);
	
}