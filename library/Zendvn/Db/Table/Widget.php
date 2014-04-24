<?php 
class Zendvn_Db_Table_Widget extends Zendvn_Db_Table_Abstract{
	protected $_name	= 'widgets';
	protected $_primary = 'id';
	
	public function getItems($location = 'site'){
		
		if($location == 'admin'){
			return $this->fetchAll($this->select()->setIntegrityCheck(false)
					->from('widgets')
					->joinLeft('extensions', 'extensions.id = widgets.extension_id', array('name' => 'extensions.name'))
					->where('extensions.location = ?', $location)
					->order('order')
					->group('widgets.id'));
			}
		else{
			$front = Zend_Controller_Front::getInstance();
			$pid = $front->getRequest()->getParam('pid', 0);
			
			return $this->fetchAll($this->select()->setIntegrityCheck(false)
					->from('widgets')
					->joinLeft('extensions', 'extensions.id = widgets.extension_id', array('name' => 'extensions.name'))
					->joinRight('widget_menu', 'widgets.id = widget_menu.widget_id')
					->where('extensions.location = ?', $location)
					->where('widget_menu.menu_id = 0 OR widget_menu.menu_id = ?', $pid)
					->order('order')
					->group('widget_menu.widget_id')
			);	
		}
		
	}
}
?>