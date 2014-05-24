<?php 
class Templates_Helper_DisplayRow extends Zend_View_Helper_Abstract 
{
	public function displayRow($name = null, $params = null){
		$xhtml = '<div class="row">';
		//$cols = array(3,3,4,2);
		if(isset($params)){
			$cols = $params;
			$keys = array('a','b','c','d','e','f');
			
			foreach ($cols as $key => $col){
				$positionName = $name .'-' . $keys[$key];
				$class = 'col-xs-' . $col;
				$xhtml .= '<div class="' . $class . '"><position>' . $positionName . '</position></div>'; 
			}
		}
		$xhtml .= '</div>'; 
		return $xhtml;
	}
	
}
?>