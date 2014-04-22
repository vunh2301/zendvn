<?php 
class Zendvn_Navigation_Page extends Zend_Navigation_Page_Mvc{
	public function isActive($recursive = false)
	{
		if (null === $this->_active) {
			$front     = Zend_Controller_Front::getInstance();
			$request   = $front->getRequest();
			$reqParams = array();
			if ($request) {
				$reqParams = $request->getParams();
				if (!array_key_exists('module', $reqParams)) {
					$reqParams['module'] = $front->getDefaultModule();
				}
			}
	
			$myParams = $this->_params;
	
			if ($this->_route) {
				$route = $front->getRouter()->getRoute($this->_route);
				if(method_exists($route, 'getDefaults')) {
					$myParams = array_merge($route->getDefaults(), $myParams);
				}
			}
	
			if (null !== $this->_module) {
				$myParams['module'] = $this->_module;
			} elseif(!array_key_exists('module', $myParams)) {
				$myParams['module'] = $front->getDefaultModule();
			}
	
			if (null !== $this->_controller) {
				$myParams['controller'] = $this->_controller;
			} elseif(!array_key_exists('controller', $myParams)) {
				$myParams['controller'] = $front->getDefaultControllerName();
			}
	
			if (null !== $this->_action) {
				$myParams['action'] = $this->_action;
			}

			foreach($myParams as $key => $value) {
				if($value == null) {
					unset($myParams[$key]);
				}
			}
			
			if (count(array_intersect_assoc($reqParams, $myParams)) == count($myParams)) {
				$this->_active = true;
				return true;
			}
			$this->_active = false;
		}
	
		return parent::isActive($recursive);
	}
	
	/**
	 * Returns href for this page
	 *
	 * This method uses {@link Zend_Controller_Action_Helper_Url} to assemble
	 * the href based on the page's properties.
	 *
	 * @return string  page href
	 */
	public function getHref()
	{
		if ($this->_hrefCache) {
			return $this->_hrefCache;
		}
	
		if (null === self::$_urlHelper) {
			self::$_urlHelper =
			Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
		}
	
		$params = $this->getParams();
	
		if ($param = $this->getModule()) {
			$params['module'] = $param;
		}
	
		if ($param = $this->getController()) {
			$params['controller'] = $param;
		}
	
		if ($param = $this->getAction()) {
			$params['action'] = $param;
		}
		if(isset($params['action'])) $params['action'] = 'index';

		$url = self::$_urlHelper->url($params,
				$this->getRoute(),
				$this->getResetParams(),
				$this->getEncodeUrl());
	
		// Use scheme?
		$scheme = $this->getScheme();
		if (null !== $scheme) {
			if (null === self::$_schemeHelper) {
				require_once 'Zend/View/Helper/ServerUrl.php';
				self::$_schemeHelper = new Zend_View_Helper_ServerUrl();
			}
	
			$url = self::$_schemeHelper->setScheme($scheme)->serverUrl($url);
		}
	
		// Add the fragment identifier if it is set
		$fragment = $this->getFragment();
		if (null !== $fragment) {
			$url .= '#' . $fragment;
		}
	
		return $this->_hrefCache = $url;
	}
	public function getPrivilege()
	{
		if(!$this->_privilege){
			return 'access';
		}
		else
			return $this->_privilege;
	}
	
	public function getResource()
	{
		if(!$this->_resource && $this->getModule()){
			return $this->getModule();
		}
		else
			return $this->_resource;
	}
}
?>