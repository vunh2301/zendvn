<?php 
class Zendvn_Controller_Plugin_Template extends Zend_Controller_Plugin_Abstract{
	public function preDispatch(Zend_Controller_Request_Abstract $request){
		$viewRenderer   = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$view 			= $viewRenderer->view;
		if(Zendvn_Factory::getLocation() == 'site' && ($pid = $request->getParam('pid')) > 0 && ($menu = Zendvn_Menu::getInstance()->getMenu($pid)) != null && $menu->template_name != null){
			$templateParams = json_decode($menu->template_params, true);
			$view->assign((array)$templateParams);
			$view->addHelperPath(APPLICATION_PATH . '/templates/' . $menu->template_name . '/helpers', 'Templates_Helper');
			$view->layout()->setLayout($menu->template_name . "/layout");
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