<?php
class Zend_View_Helper_WidgetBreadcrumb extends Zend_View_Helper_Abstract
{
    public function WidgetBreadcrumb()
    {
    	$container = Zendvn_Factory::getNavigation();
		$breadcrumbs = $this->view->navigation($container)
				//->setAcl(Library_Factory::getAcl())
				//->setRole('current-user')
				->breadcrumbs()
          		//->setMaxDepth(1)
				->setMinDepth(0)
          		->setSeparator('</li>' . PHP_EOL . '<li class="active">');
		if(Zendvn_Factory::getBreadcrumb()){
			return $breadcrumbs->setLinkLast(true);
		}
		return $breadcrumbs;
    }
}