<?php 
class Zendvn_Controller_Plugin_Template extends Zend_Controller_Plugin_Abstract{
	public function preDispatch(Zend_Controller_Request_Abstract $request){
		$viewRenderer   = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$view 			= $viewRenderer->view;
		$appConfig		= Zendvn_Factory::getAppConfig();
		if(Zendvn_Factory::getLocation() == 'site' && ($pid = $request->getParam('pid')) > 0 && ($menu = Zendvn_Factory::getMenu()->getMenu($pid)) != null && $menu->template_name != null){
			$templateParams = json_decode($menu->template_params, true);
			$view->assign((array)$templateParams);
			$view->addHelperPath(APPLICATION_PATH . '/templates/' . $menu->template_name . '/helpers', 'Templates_Helper');
			$view->layout()->setLayout($menu->template_name . "/layout");
			$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')->appendHttpEquiv('Content-Language', 'en-US');
			$view->headMeta()->appendName('generator', 'Zendvn Cms');
			if(isset($appConfig['site']['robots']) && $appConfig['site']['robots'] != null)$view->headMeta()->appendName('robots', $appConfig['site']['robots']);
			if(isset($appConfig['site']['contentRights']) && $appConfig['site']['contentRights'] != null)$view->headMeta()->appendName('rights', $appConfig['site']['contentRights']);
			if(isset($appConfig['site']['metaDescription']) && $appConfig['site']['metaDescription'] != null)$view->headMeta()->appendName('description', $appConfig['site']['metaDescription']);
			if(isset($appConfig['site']['metaKeywords']) && $appConfig['site']['metaKeywords'] != null)$view->headMeta()->appendName('keywords', $appConfig['site']['metaKeywords']);
			if(isset($appConfig['site']['name']))$view->headTitle($appConfig['site']['name'])->setSeparator(' - ');
		}else{	
			$tblTemplate = new Zendvn_Db_Table_Template();
			$template = $tblTemplate->getDefaultTemplate();
			$templateParams = json_decode($template->params, true);
			$view->assign((array)$templateParams);
			$view->addHelperPath(APPLICATION_PATH . '/templates/' . $template->template . '/helpers', 'Templates_Helper');
			$view->layout()->setLayout($template->template . "/layout");
		}
	}
}
?>