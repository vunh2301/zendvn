<?php

class Contents_CategoryController extends Zend_Controller_Action
{
	public function indexAction()
    {
    	$id = (int)$this->_request->getParam('cid', 0);
    	$tblCategory = new Contents_Model_DbTable_Category();
    	$category = $tblCategory->getCategory($id);
    	$this->view->headTitle($category->title);
    	$this->view->paginator = $category->getArticles($this->_request->getParam('page',1));
    	$this->view->category = $category;
    	
    	//Zend_Debug::dump($this->_request->getParams());
    	
    	/*
    	Zend_Debug::dump(
	    	$this->view->route(
			    	array(
				    	'module' => 'contents',
				    	'controller' => 'category',
				    	'action' => 'article',
				    	'cid' => 6,
				    	'id' => 1,
				    	'alias' => 'article'
	    			)
	    	)
    	);
    	*/
    }
    
    public function articleAction()
    {
    	Zendvn_Factory::addBreadcrumb(array(array('label' => 'Article')));
    	Zend_Debug::dump($this->_request->getParams());
    }
}
/*
 		Zend_Debug::dump(
	    	$this->view->route(
		    	array(
		    		'module' => 'contents',
		    		'controller' => 'category',
		    		'action' => 'article',
		    		'cid' => 3,
		    		'id' => 1,
		    		'alias' => 'article'
		    	)
		    )
    	);
		Zend_Debug::dump(
	    	$this->view->route(
		    	array(
		    		'module' => 'contents',
		    		'controller' => 'category',
		    		'action' => 'article',
		    		'cid' => 2,
		    		'id' => 2,
		    		'alias' => 'article 2'
		    	)
		    )
    	);
 */