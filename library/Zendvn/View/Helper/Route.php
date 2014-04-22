<?php

class Zendvn_View_Helper_Route extends Zend_View_Helper_Abstract
{
    public function route(array $urlOptions = array(), $name = null, $reset = true, $encode = true)
    {
    	$front = Zend_Controller_Front::getInstance();
    	$router = $front->getRouter();
    	$routesHasUrlOptions = array();
    	if(!$urlOptions['module'])$urlOptions['module'] = $front->getRequest()->getModuleName();
    	if(!$urlOptions['controller'])$urlOptions['controller'] = $front->getRequest()->getControllerName();
    	// get all route
    	foreach ($router->getRoutes() as $key => $route){
    		$defaults = $route->getDefaults();  		
    		//if(!$defaults['action'])$defaults['action'] = 'index';
    		if ($urlOptions['module'] == $defaults['module'] && $urlOptions['controller'] == $defaults['controller'] && $urlOptions['action'] == $defaults['action']) {
    			$variables = $route->getVariables();
    			unset($defaults['module'], $defaults['controller'], $defaults['action'], $defaults['pid']);
    			if($variables != null){
    				foreach ($variables as $variable){
    					unset($defaults[$variable]);
    				}
    			}
    			// Truong hop route co param
    			if($defaults != null){
    				$isMatch = true;
    				foreach ($defaults as $keyDef => $valueDef){
    					if(!$urlOptions[$keyDef] || $urlOptions[$keyDef] != $valueDef)
    						$isMatch = false;
    				}
    				if($isMatch == true)
    					$routesHasUrlOptions[$key] = $route;
    			}
    			// Truong hop route ko co param
    			else{
    				$routesHasUrlOptions[$key] = $route;
    			}
    			
    		} 
    	}
    	if($routesHasUrlOptions != null){
    		$activeRoute = reset($routesHasUrlOptions);
    		return '/' . $activeRoute->assemble($urlOptions, $reset, $encode);
    	}else{
    		return $router->assemble($urlOptions, 'default', $reset, $encode);
    	}
        return 'root route';
    }
}