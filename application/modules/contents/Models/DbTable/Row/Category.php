<?php
class Contents_Model_DbTable_Row_Category extends Zendvn_Db_Table_Row_Abstract
{
	private $_userTimezone;
	
	public function init()
	{
		$user = Zendvn_Factory::getUser();
		if($user->isGuest() === true){
			$appConfig = Zendvn_Factory::getAppConfig();
			$this->_userTimezone = $appConfig['site']['timezone'];
		}else{
			$this->_userTimezone  = $user->params['timezone'];
		}
	}
	
	public function __get($columnName)
	{
		$info = $this->_table->info();
		$metadata = $info['metadata'];
		$data = parent::__get($columnName);
		if(isset($metadata[$columnName]['DATA_TYPE']) && $metadata[$columnName]['DATA_TYPE'] == 'datetime'){
			if($data != '0000-00-00 00:00:00'){
				$date = new Zend_Date($data);
				$data = $date->setTimezone($this->_userTimezone)->toString('dd-MM-yyyy hh:mm a');
			}else{
				$data = '';
			}
		}
		return $data;
	}
	
	public function toArray()
	{
		$info = $this->_table->info();
		$metadata = $info['metadata'];
		$data = (array)$this->_data;
		$_data = array();
		foreach ($data as $columnName => $value){
			if(isset($metadata[$columnName]['DATA_TYPE']) && $metadata[$columnName]['DATA_TYPE'] == 'datetime'){
				if($value != '0000-00-00 00:00:00' && $value != null){
					$date = new Zend_Date($value);
					$_data[$columnName] = $date->setTimezone($this->_userTimezone)->toString('dd-MM-yyyy hh:mm a');
				}else{
					$_data[$columnName] = '';
				}
			}else{
				$_data[$columnName] = $value;
			}
		}
		return $_data;
	}
	
	public function getGroupAccess()
	{
		$acl = Zendvn_Factory::getAcl();
		$roles = $acl->getRoles();
		$groups = array();
		$all = true;
		foreach($roles as $role){
			if($acl->isAllowed($role,'contents.categories.' . $this->id, 'access')){
				$groups[str_replace('group_', '', $role)] = $acl->getRole($role)->getRoleName();
			}else{
				$all = false;
			}
		}
		if(true === $all) {
			return '*';
		}
		return $groups;
	}
	
	public function hasRule()
	{
		$acl = Zendvn_Factory::getAcl();
		return $acl->hasRule('contents.categories.' . $this->id);
	}
	
	public function getParams(){
		return json_decode($this->params, true);
	}
	
	protected $_articles;
	public function getArticles($page = 1){
		$params =  $this->getParams();
		$perPage = (int)$params['links'] + (int)$params['intro-list'] + (int)$params['leading'] + (int)$params['intro-grid'];
		if(null === $this->_articles){
			$tblArticle = new Contents_Model_DbTable_Article();
			$this->_articles = $tblArticle->getItems(array(
					'category' => $this->id,
					'status' => 'publish',
					'paginator' => $page,
					'paginator_per_page' => $perPage
			));
		}
		return $this->_articles;
	}
	
	public function getLeadingArticles(){
		$articles = $this->getArticles();
		$params = $this->getParams();
		$leading = array();
		$numLeading = (int)$params['leading'];
		foreach($articles as $index => $article){
			if($numLeading == $index) break;
			$leading[] = $article;
		}
		return $leading;
	}
	
	public function getIntroGridArticles(){
		$articles = $this->getArticles();
		$params = $this->getParams();
		$intro = array();
		$numLeading = (int)$params['leading'];
		$numIntro = (int)$params['intro-grid'];		
		foreach($articles as $index => $article){
			if(($numIntro + $numLeading) == $index) break;
			if($numLeading > $index) continue;
			$intro[] = $article;
		}
		return $intro;
	}
	
	public function getIntroListArticles(){
		$articles = $this->getArticles();
		$params = $this->getParams();
		$intro = array();
		$numLeading = (int)$params['leading'];
		$numIntroGrid = (int)$params['intro-grid'];
		$numIntro = (int)$params['intro-list'];
		foreach($articles as $index => $article){
			if(($numIntro + $numLeading + $numIntroGrid) == $index) break;
			if($numLeading + $numIntroGrid > $index) continue;
			$intro[] = $article;
		}
		return $intro;
	}
	
	public function getLinkArticles(){
		$articles = $this->getArticles();
		$params = $this->getParams();
		$links = array();
		$numLinks = (int)$params['links'];
		$numLeading = (int)$params['leading'];
		$numIntroGrid = (int)$params['intro-grid'];
		$numIntroList = (int)$params['intro-list'];
		foreach($articles as $index => $article){
			if(count($links) == $numLinks) break;
			if($numIntroList + $numLeading + $numIntroGrid > $index) continue;
			$links[] = $article;
		}
		return $links;
	}
}

?> 