<?php 
class Templates_Helper_DisplayRow extends Zend_View_Helper_Abstract 
{
	public function displayRow($name = null, $min = false){
		$params = $this->view->$name;
		$xhtml = '';
		if(isset($params) && $params != null){
			$cols = explode("|", $params);
			$keys = array('a','b','c','d','e','f');
			if(in_array("M", $cols)){
				$mailCol = 12;
				foreach($cols as $col)$mailCol = $mailCol - (int)$col;
			}
			if($min === true){
				$xhtml = '<div class="row">';
				foreach ($cols as $key => $col){
					if($col == 'M'){
						$xhtml .= '<div class="col col-xs-' . $mailCol . '">';
						$xhtml .= $this->displayRow('content_top', true);
						$xhtml .= '<div class="row">';
						$xhtml .= '	<div class="col col-xs-12">';
						$xhtml .= '		<main/>';
						$xhtml .= '	</div>';
						$xhtml .= '</div>';
						$xhtml .= $this->displayRow('content_bottom', true);
						$xhtml .= '</div>';
					}else{
						$positionName = $name .'-' . $keys[$key];
						$class = 'col-xs-' . $col;
						$xhtml .= '<div class="col ' . $class . '"><position>' . $positionName . '</position></div>';
					}
				}
				$xhtml .= '</div>';
			}else{
				$xhtml = '';
				if(in_array("M", $cols)){
					$isNull = false;
				}else{
					$isNull = true;
				}
				
				foreach ($cols as $key => $col){
					if($col == 'M'){
						$xhtml .= '<div class="col col-md-' . $mailCol . '">';
						$xhtml .= $this->displayRow('content_top');
						$xhtml .= '<div class="row">';
						$xhtml .= '	<div class="col col-md-12">';
						$xhtml .= $this->view->layout()->content;
						$xhtml .= '	</div>';
						$xhtml .= '</div>';
						$xhtml .= $this->displayRow('content_bottom');
						$xhtml .= '</div>';
					}else{
						$positionName = $name .'-' . $keys[$key];
						$class = 'col-md-' . $col;
						$widgetHtml = $this->view->widget($positionName);
						if($widgetHtml != null)$isNull = false;
						$xhtml .= '<div class="col ' . $class . '">' . $widgetHtml . '</div>';
					}
				}
				$xhtml = $isNull ? '' : '<div class="row">' . $xhtml . '</div>';
			}
		}
		return $xhtml;
	}
	

	
}
?>