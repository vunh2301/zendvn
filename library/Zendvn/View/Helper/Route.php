<?php

class Zendvn_View_Helper_Route extends Zend_View_Helper_Abstract
{
    public function route(array $urlOptions = array(), $name = null, $reset = true, $encode = true)
    {
    	$front = Zend_Controller_Front::getInstance();
    	$router = $front->getRouter();
    	$routesHasUrlOptions = array();
    	$routeDefault = null;
    	
    	if($reset){
	    	if(isset($urlOptions['module']) === false)$urlOptions['module'] = $front->getRequest()->getModuleName();
	    	if(isset($urlOptions['controller']) === false)$urlOptions['controller'] = $front->getRequest()->getControllerName();    	
	    	if(isset($urlOptions['action']) === false)$urlOptions['action'] = $front->getRequest()->getActionName();
    	}else{
    		$urlOptions = array_merge($front->getRequest()->getParams(), $urlOptions);
    	}
    	// get all route
    	foreach ($router->getRoutes() as $key => $route){
    		$defaults = $route->getDefaults();  
    		if($key != 'default'){
	    		if(isset($defaults['action']) === false)$defaults['action'] = 'index';		 		
	    		if ($urlOptions['module'] == $defaults['module'] && $urlOptions['controller'] == $defaults['controller'] && $urlOptions['action'] == $defaults['action']) {
	    			unset($defaults['module'], $defaults['controller'], $defaults['action'], $defaults['pid']);
	    			
	    			if(method_exists($route,'getVariables')){
		    			$variables = $route->getVariables();
		    			if($variables != null){
		    				foreach ($variables as $variable){
		    					unset($defaults[$variable]);
		    				}
		    			}
	    			}
	    			// Truong hop route co param
	    			if($defaults != null){
	    				$isMatch = true;
	    				foreach ($defaults as $keyDef => $valueDef){
	    					if(isset($urlOptions[$keyDef]) === false || $urlOptions[$keyDef] != $valueDef)
	    						$isMatch = false;
	    				}
	    				if($isMatch == true){
	    					$routesHasUrlOptions[$key] = $route;
	    				}
	    			}
	    			// Truong hop route ko co param
	    			else{
	    				$routesHasUrlOptions[$key] = $route;	    				
	    			}
	    			
	    		} 
    		}else{
    			$routeDefault = $route;
    		}
    	}
    	if($routesHasUrlOptions != null){
    		if(count($routesHasUrlOptions) > 1){
    			foreach ($routesHasUrlOptions as $routesHasUrlOption){
    				$isMatch = true;
	    			if($routesHasUrlOption instanceof Zend_Controller_Router_Route_Static){
	    				$defaults = $routesHasUrlOption->getDefaults();
	    				
	    				foreach ($urlOptions as $key => $urlOption){
	    					if(!isset($defaults[$key]) && $key != 'action') $isMatch = false;
	    				}
	    			}elseif($routesHasUrlOption instanceof Zend_Controller_Router_Route_Regex){
	    				$defaults = $routesHasUrlOption->getDefaults();
	    				$variables = array_flip ($routesHasUrlOption->getVariables());
	    				foreach ($urlOptions as $key => $urlOption){
	    					if(!isset($defaults[$key]) || !isset($variables[$key])) $isMatch = false;
	    				}
	    			}
	    			if($isMatch === true)return '/' . $routesHasUrlOption->assemble($urlOptions, $reset, $encode);
    			}
    		}
    		$activeRoute = reset($routesHasUrlOptions);
    		return '/' . $activeRoute->assemble($urlOptions, $reset, $encode);
    	}else{
    		return '/' . $routeDefault->assemble($urlOptions, 'default', $reset, $encode);
    	}
    }
}