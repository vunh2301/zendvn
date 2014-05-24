<?php
class Zend_View_Helper_ToNavigation extends Zend_View_Helper_Abstract
{
	public function toNavigation($menus){
		if($menus instanceof Zend_Db_Table_Rowset_Abstract){
			$menus = $menus->toArray();
		}
		$container = new Zend_Navigation();
		if(is_array($menus)){
			foreach ($menus as $menu){
				$query = json_decode($menu['query'], true);
				if($menu['level'] > 1){
					$container->findById($menu['parent_id'])->addPage(array(
							'label' => $menu['title'],
							'type' => 'Zendvn_Navigation_Page',
							'id' => $menu['id'],
							'module' => $menu['module'],
							'controller' => $menu['controller'],
							'params' => array_merge(array('pid' => $menu['id']), (array)$query),
							'route' => $menu['module'] . '_' . $menu['controller'] . '_index_' . $menu['id']
					));
				}else{
					$container->addPage(array(
							'label' => $menu['title'],
							'type' => 'Zendvn_Navigation_Page',
							'id' => $menu['id'],
							'module' => $menu['module'],
							'controller' => $menu['controller'],
							'params' => array_merge(array('pid' => $menu['id']), (array)$query),
							'route' => $menu['module'] . '_' . $menu['controller'] . '_index_' . $menu['id']
					));
				}
			}
		}
		
		return $container;
	}
}