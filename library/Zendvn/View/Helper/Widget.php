<?php

class Zendvn_View_Helper_Widget extends Zend_View_Helper_Abstract
{
    public function widget($position = null)
    {
    	$location = Zendvn_Factory::getLocation();
    	$widgets = Zendvn_Factory::getWidgets();
    	$html = '';
    	if($widgets->count()>0){
    		foreach ($widgets as $widget){
    			if($position == $widget->position){
	    			$params = json_decode($widget->params, true);
	    			$view = clone $this->view;
	    			$view->params = new Zendvn_Object($params);
	    			$view->widget = $widget;
	    			//add Script Path default view
	    			if($location == 'site'){
	    				$view->setScriptPath(APPLICATION_PATH."/widgets/" . $widget->name . "/scripts/");
	    				$view->addHelperPath(APPLICATION_PATH."/widgets/" . $widget->name . "/helpers/");
	    			}else{
	    				$view->setScriptPath(APPLICATION_PATH."/administrator/widgets/" . $widget->name . "/scripts/");
	    				$view->addHelperPath(APPLICATION_PATH."/administrator/widgets/" . $widget->name . "/helpers/");
	    			}
	    			$html .= $view->render("index.phtml");
    			}
    		}
    	}
    	return $html;
    }
}