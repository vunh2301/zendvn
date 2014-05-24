<?php
class Zend_View_Helper_WidgetMenu extends Zend_View_Helper_Abstract
{
    public function widgetMenu()
    {
    	$container = Zendvn_Factory::getNavigation();
		return $this->view->menu($container)
				->setAcl(Zendvn_Factory::getAcl())
				->setRole('current-user')
				->setUlClass('nav navbar-nav')
				->addPageClassToLi(true);
    }
    
}