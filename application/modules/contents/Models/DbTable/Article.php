<?php
class Contents_Model_DbTable_Article extends Zendvn_Db_Table_Abstract
{
	protected $_name		= 'articles';
	
	//protected $_rowsetClass = 'Contents_Model_DbTable_Rowset_Articles';
	
	protected $_rowClass 	= 'Contents_Model_DbTable_Row_Article';
	
	protected $_referenceMap    = array(
			'Category' => array(
					'columns'           => array('category_id'),
					'refTableClass'     => 'Contents_Model_DbTable_Category',
					'refColumns'        => array('id')
			)
	);
	
	private $_globalTimezone;
	
	private $_categories;

	public function init(){
		$appConfig = Zendvn_Factory::getAppConfig();
		$this->_globalTimezone = $appConfig['site']['timezone'];
	}
	
	public function getItems($filter){
		$select = $this->select()
		->from('articles')
		->setIntegrityCheck(false)
		->joinLeft('categories', 'articles.category_id = categories.id', array('category' => 'categories.title'))
		->joinLeft(array('cusers' => 'users'), 'articles.created_user_id = cusers.id', array('created_user' => 'cusers.real_name'))
		->group('articles.id');

		// Filter Search
		if(isset($filter['search']) && null != $filter['search'])
			$select->where('articles.title LIKE("%' . $filter['search'] . '%")');

		// Filter Category
		if(isset($filter['category']) && $filter['category'] != '*')
			$select->where('articles.category_id = ?', $filter['category']);
		
		// Filter Status
		if(isset($filter['status']) && $filter['status'] != '*')
			$select->where('articles.status = ?', $filter['status']);
		
		// Filter Featured
		if(isset($filter['featured']) && $filter['featured'] != '*')
			$select->where('articles.featured = ?', (int)$filter['featured']);
		
		// Ordering
		if(isset($filter['ordering']) && isset($filter['order_by']) && null !== $filter['ordering'] && null !== $filter['order_by'])
			$select->order($filter['ordering'] . ' ' . $filter['order_by']);
		
		// Paging
		if(isset($filter['paginator']) && isset($filter['paginator_per_page'])){
			$adapter 	= new Zend_Paginator_Adapter_DbTableSelect($select);
			$paginator 	= new Zend_Paginator($adapter);
			$paginator->setCurrentPageNumber($filter['paginator'])->setItemCountPerPage($filter['paginator_per_page']);
			return $paginator;
		}else{
			return $this->fetchAll($select);
		}
	}
	
}

?>