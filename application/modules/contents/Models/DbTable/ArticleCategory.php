<?php

class Contents_Model_DbTable_ArticleCategory extends Zendvn_Db_Table_Abstract
{
	protected $_name			= 'article_category';

	protected $_referenceMap    = array(
        'Article' => array(
            'columns'           => array('article_id'),
            'refTableClass'     => 'Contents_Model_DbTable_Article',
            'refColumns'        => array('id'),
			'onDelete'          => self::CASCADE,
			'onUpdate'          => self::RESTRICT
        ),
        'Category' => array(
            'columns'           => array('category_id'),
            'refTableClass'     => 'Contents_Model_DbTable_Category',
            'refColumns'        => array('id'),
			'onDelete'          => self::CASCADE,
			'onUpdate'          => self::RESTRICT
        )
    );
}

?>