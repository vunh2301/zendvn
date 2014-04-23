<?php
class Zend_View_Helper_Order extends Zend_View_Helper_Abstract
{
    public function order($title, $name)
    {	
    	if($this->view->filter['order_by'] == 'ASC'){
			$val = 'DESC';
			$icon = '<span class="glyphicon glyphicon-sort-by-attributes"></span>';
		}else{
			$val = 'ASC';
			$icon = '<span class="glyphicon glyphicon-sort-by-attributes-alt"></span>';
		}
    	if($this->view->filter['ordering'] == $name){
    		$href = "$('#order_by').val('" . $val . "');";
    		$html = '<a href="#" onclick="$(\'#order_by\').val(\'' . $val . '\'); $(\'#ordering\').val(\'' . $name . '\'); $(this).closest(\'form\').submit(); return false;">' . $title . ' ' . $icon . '</a>';
    	}else{
			$html = '<a href="#" onclick="$(\'#ordering\').val(\'' . $name . '\'); $(this).closest(\'form\').submit(); return false;">' . $title . '</a>';
		}
    	return $html;
    }
}