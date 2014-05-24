<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{  
	protected function _initRequest(){
		$front          = Zend_Controller_Front::getInstance();
		$front->setRequest(new Zend_Controller_Request_Http());
		$request = $front->getRequest()->setBaseUrl();
		$requestUriPath = explode('/', str_replace($front->getRequest()->getBaseUrl(), '', $front->getRequest()->getRequestUri()));
		$environment = $requestUriPath[1];
		if($environment == 'administrator'){
			Zend_Registry::set('Zendvn_Location', 'admin');
		}else{
			Zend_Registry::set('Zendvn_Location', 'site');
		}
		return $request;
	}
	
	protected function _initFrontController()
	{
		$localion 	= Zend_Registry::get('Zendvn_Location');
		$front      = Zend_Controller_Front::getInstance();	
		if($localion == 'admin'){
			$front->addModuleDirectory(APPLICATION_PATH . '/administrator/modules');
			$front->setDefaultModule('admin')->setDefaultControllerName('index')->setDefaultAction('index')->setParam('displayExceptions', true);
		}else{
			$front->addModuleDirectory(APPLICATION_PATH . '/modules');
			$front->setDefaultModule('index')->setDefaultControllerName('index')->setDefaultAction('index')->setParam('displayExceptions', true);
		}
		return $front;
	}
	
	protected function _initBootstrap(){
		$this->bootstrap('db');
		$this->bootstrap('layout');
		$config = $this->getApplication()->getOptions();
		date_default_timezone_set($config['site']['timezone']);
	}
	
	protected function _initAutoloadNamespaces()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace("Zendvn");
	}
	
	protected function _initAutoloadResources()
	{
		$autoloader = new Zend_Application_Module_Autoloader(array(
				'namespace' => '',
				'basePath' => APPLICATION_PATH));
		$autoloader
		->addResourceType( 'template', 'templates', 'Template')
		->addResourceType( 'plugin', 'plugins', 'Plugin')
		->addResourceType( 'widget', 'widgets', 'Widget')
		->addResourceType( 'administrator/widget', 'widgets', 'Widget');
	
		$view = $this->getResource('layout')->getView();
		$view->addHelperPath('Zendvn/View/Helper','Zendvn_View_Helper_');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
		
		return $autoloader;
	}
	
	protected function _initCache(){
		$manager = new Zend_Cache_Manager();
		Zend_Registry::set('Zendvn_Cache', $manager);
	}
	
	protected function _initRouter(){
		$front          = Zend_Controller_Front::getInstance();
		$router         = $front->getRouter();
		$location		= Zendvn_Factory::getLocation();
		$front->setRequest(new Zend_Controller_Request_Http());
		$request 		= $front->getRequest()->setBaseUrl();
		$cache 			= Zendvn_Factory::getCache('Cms');
		if($location === 'admin'){
			$route = new Zend_Controller_Router_Route(
					'administrator/:module/:controller/:action/*',
					array(
							'module'  => 'admin',
							'controller' => 'index',
							'action' => 'index'
					)
			);
			$router->addRoute('default', $route);
		}else{
			$requestUri     = $request->getRequestUri();
			$config    		= $this->getApplication()->getOptions();
			$homeMenu		= Zendvn_Factory::getMenu()->getHomeMenu();
			$suffix			= $config["site"]['route']['suffix'];
			$shortUrl		= $config["site"]['route']['shortUrl'];
			$urlRewrite		= $config["site"]['route']['urlRewrite'];
			
			if($urlRewrite == false){
				if(str_replace($_SERVER['REQUEST_URI'], '', $_SERVER['SCRIPT_NAME']) == 'index.php'){
					$request->setParams(array_merge(array('pid' => $homeMenu['id']), json_decode($homeMenu['query'], true)));
				}
				return;
			}
			if( str_replace($_SERVER['REQUEST_URI'], '', $_SERVER['SCRIPT_NAME']) == 'index.php'){
				$requestUri = '/' . $homeMenu['alias'] . $suffix;
				$request->setRequestUri($requestUri);
			}

			$menus = Zendvn_Factory::getMenu()->getMenus();
			foreach ($menus as $menu){
				$alias 			= $menu->alias;
				$moduleName 	= $menu->module;
				$controllerName = $menu->controller;
				$query = json_decode($menu->query, true);
			
				$defRoute = new Zend_Controller_Router_Route_Static(
						$alias . $suffix,
						array_merge(array(
								'module' => $moduleName,
								'controller' => $controllerName,
								'pid' => $menu->id
						), $query)
				);
				//Ext Routes
				$extRoutePath = APPLICATION_PATH . '/modules/' . $moduleName . '/routes';
				if (($extRoutes = Zendvn_Config::factory($extRoutePath, null, array('skipExtends' => true,'allowModifications' => true))) !== null) {
					if(isset($extRoutes->routes)){
						foreach ($extRoutes->routes as $name => $route){
							$routeConfig = $route->toArray();
							$routeConfig['route'] = $alias . $routeConfig['route'] . $suffix;
							if($route->type == 'Zend_Controller_Router_Route_Regex')
								$routeConfig['reverse'] = $alias . $routeConfig['reverse'] . $suffix;
							$routeConfig['defaults']['module'] = $moduleName;
							$routeConfig['defaults']['controller'] = $controllerName;
							if($name != 'index') $routeConfig['defaults']['action'] = $name;
							else $routeConfig['defaults']['action'] = 'index';
							$routeConfig['defaults']['pid'] = $menu->id;
							$routeConfig['defaults'] = array_merge($routeConfig['defaults'], $query);
							$config = new Zend_Config(array($moduleName . '_' . $controllerName . '_' . $name . '_' . $menu->id => $routeConfig));
							$router->addConfig($config);
						}
					}
				}
				$router->addRoute('def_' . $moduleName . '_' . $controllerName . '_index_' . $menu->id, $defRoute);
			}
		}
		return $router;
	}
	
	protected function _initControllerPlugin()
	{
		$front          = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new Zendvn_Controller_Plugin_Auth());
		$front->registerPlugin(new Zendvn_Controller_Plugin_Template());
	}
	
}
