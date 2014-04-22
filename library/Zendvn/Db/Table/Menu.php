<?php 
class Zendvn_Db_Table_Menu extends Zendvn_Db_Table_NestedSet{
	protected $_name	= 'menu';
	protected $_primary = 'id';
	
	public function getItems(){
		return $this->fetchAll($this->select()->setIntegrityCheck(false)
				->from('menu')
				->where('lft > 0')
				->joinLeft(array('ext' => 'extensions'), 'ext.id = menu.module_id', array('module' => 'ext.name'))
				->joinLeft(array('tpl' => 'templates'), 'tpl.id = menu.template_id', array('template_params' => 'tpl.params'))
				->joinLeft(array('ext2' => 'extensions'), 'tpl.extension_id = ext2.id', array('template_name' => 'ext2.name'))
				->order('lft')
				->group('menu.id'));
	}
}
?>