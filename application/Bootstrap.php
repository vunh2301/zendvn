<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{  
	protected function _initBootstrap(){
		$this->bootstrap('frontController');
		$this->bootstrap('db');
		$this->bootstrap('layout');
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
	
	protected function _initRouter(){
		$front          = Zend_Controller_Front::getInstance();
		$router         = $front->getRouter();
		$location		= Zendvn_Factory::getLocation();
		$front->setRequest(new Zend_Controller_Request_Http());
		$request 		= $front->getRequest()->setBaseUrl();
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
			$homeMenu		= Zendvn_Menu::getInstance()->getHomeMenu();
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

			$menus = Zendvn_Menu::getInstance()->getMenus();
			
			foreach ($menus as $menu){
				$alias 			= $menu->alias;
				$moduleName 	= $menu->module;
				$controllerName = $menu->controller;
				$query = json_decode($menu->query, true);
			
				$route = new Zend_Controller_Router_Route(
						$alias . $suffix,
						array_merge(array(
								'module' => $moduleName,
								'controller' => $controllerName,
								'pid' => $menu->id
						), $query)
				);
				$router->addRoute($moduleName . '_' . $controllerName . '_index_' . $menu->id, $route);
				//Ext Routes
				$extRoutePath = APPLICATION_PATH . '/modules/' . $moduleName . '/routes.xml';
				if(file_exists($extRoutePath)){
					$extRoutes = new Zend_Config_Xml($extRoutePath, null, array('skipExtends' => true,'allowModifications' => true));
					if(isset($extRoutes)){
						foreach ($extRoutes as $name => $route){
							if($route->route != null){
								$routeConfig = $route->route->toArray();
								$routeConfig['route'] = $alias . '/' . $routeConfig['route'] . $suffix;
								$routeConfig['reverse'] = $alias . '/' . $routeConfig['reverse'] . $suffix;
								$routeConfig['defaults']['module'] = $moduleName;
								$routeConfig['defaults']['controller'] = $controllerName;
								if($name != 'index') $routeConfig['defaults']['action'] = $name;
								$routeConfig['defaults']['pid'] = $menu->id;
								$routeConfig['defaults'] = array_merge($routeConfig['defaults'], $query);
								$config = new Zend_Config(array($moduleName . '_' . $controllerName . '_' . $name . '_' . $menu->id => $routeConfig));
								$router->addConfig($config);
							}
						}
					}
				}
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
