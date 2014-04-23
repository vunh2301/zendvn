<?php
class Zendvn_Acl extends Zend_Acl {
	
	protected $_defaulAdminPrivileges = array(
			'access' => array(
					'title' => 'Access Site'
			),
			'admin' => array(
					'title' => 'Access Admin'
			),
			'manager' => array(
					'title' => 'Manager'
			),
			'config' => array(
					'title' => 'Config'
			),
			'create' => array(
					'title' => 'Create'
			),
			'delete' => array(
					'title' => 'Delete'
			),
			'edit' => array(
					'title' => 'Edit'
			),
			'editOwn' => array(
					'title' => 'Edit Owner'
			),
			'editState' => array(
					'title' => 'Edit State'
			)
	);
	
	private static $_instance = null;
	
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
	
		return self::$_instance;
	}
	
	public function __construct() {
		$this->_loadRoles();
		$this->_loadResources();
		$this->_loadDefaultResource();
	}
	
	public function isAllowed($role = null, $resource = null, $privilege = null)
	{	
		if($role == 'current-user'){
			$role = Zendvn_Factory::getUser();
		}
	
		if ($role instanceof Zendvn_User) {
			$isAllow = false;
			if(true === $role->isGuest()){
				$isAllow = $this->isAllowed('group_1', $resource, $privilege);
			}else{
				foreach($role->groups as $group){
					if($group->id == 4)return true;
					$_role = 'group_' . $group->id;
					if(parent::isAllowed($_role, $resource, $privilege)){
						$isAllow = true;
					}
				}
			}
			return $isAllow;
		}else{
			return parent::isAllowed($role, $resource, $privilege);
		}
	}
	
	protected function _loadRoles(){
    	$tblGroup = new Zendvn_Db_Table_Group();
    	$groups = $tblGroup->getItems();
    	if($groups->count() > 0){
	    	foreach ($groups as $group){
	    		if($group->parent_id > 0){
	    			$this->addRole(new Zendvn_Acl_Role('group_' . $group->id, $group->title),'group_' . $group->parent_id);
	    		}else{
	    			$this->addRole(new Zendvn_Acl_Role('group_' . $group->id, $group->title));
	    		}
	    	}
    	}
    }
    
    protected function _loadResources(){
    	$tblResource = new Zendvn_Db_Table_AclResource();
    	$resources = $tblResource->getItems();
    	if($resources->count() > 0){
    		$tempResources = array();
    		foreach ($resources as $resource){
    			$assert = $resource->assert;
    			if(isset($tempResources[$resource->parent_id])){
    				if($this->has($resource->name) === false){
    					$tempResources[$resource->id] = $resource->name;
    					$this->add(new Zendvn_Acl_Resource($resource->name, $resource->id),$tempResources[$resource->parent_id]);
    				}
    			}else{
    				if($this->has($resource->name) === false){
    					$tempResources[$resource->id] = $resource->name;
    					$this->add(new Zendvn_Acl_Resource($resource->name, $resource->id));
    				}
    			}
    			if($assert != null)
    				$this->$assert('group_' . $resource->group_id, $resource->name, $resource->privilege);
    			 
    			if(null !== $resource->privilege && isset($this->_defaulAdminPrivileges[$resource->privilege]) === false){
    				$this->_defaulAdminPrivileges[$resource->privilege] = $resource->privilege;
    			}
    		}
    	}
    }
    
    protected function _loadDefaultResource(){
    	//Add roles guest
    	$this->allow('group_1', 'root', 'access');
    	
    	//Add roles user
    	$this->allow('group_2', 'root', 'access');
    	
    	//Add roles manager
    	$this->allow('group_3', 'root', 'access');
    	$this->allow('group_3', 'root', 'admin');
    	$this->allow('group_3', 'root', 'create');
    	$this->allow('group_3', 'root', 'editOwn');
    	
    	//Add roles root
    	foreach ($this->_defaulAdminPrivileges as $privilege => $value){
    		$this->allow('group_4', 'root', $privilege);
    	}
    }
    
    public function hasRule($resourceId){
    	return isset($this->_rules['byResourceId'][$resourceId]) ? true : false;
    }
    
    public function getForm($resource, $privileges = null, $name, $populate = true){
    	if(is_string($privileges) || $privileges === null){
    		$namePath 	= explode(".", $resource);
    		$aclPrivilegePath = APPLICATION_PATH . '/administrator/modules/' . $namePath[0] . '/acl.xml';
    		if(is_string($privileges)){
    			$section 	= $privileges;
    		}else{
    			$section 	= $resource;
    		}
    		if(file_exists($aclPrivilegePath)){
    			$privileges = new Zend_Config_Xml($aclPrivilegePath, $section, array('skipExtends' => true,'allowModifications' => true));
    			$privileges = $privileges->toArray();
    		}
    	}elseif($privileges instanceof Zend_Config){
    		$privileges = $privileges->toArray();
    	}

    	if(is_array($privileges) && is_string($name) && $this->has($resource)){
    		$tblGroups = new Zendvn_Db_Table_Group();
    		$groups = $tblGroups->getItems()->toArray();
    		$permissions = array();
    		if($populate){
    			$select = $tblGroups->select()->reset()->setIntegrityCheck(false)
    			->from(array('gro'=>'groups'),array('id', 'group' => 'gro.title'))
    			->joinLeft(array('pri' => 'acl_privileges'), 'gro.id = pri.group_id', array('privilege', 'assert'))
    			->joinLeft(array('res' => 'acl_resources'), 'pri.resource_id = res.id', array('resourceTitle' => 'res.title'))
    			->where('res.name = ?', $resource)
    			->order('gro.lft');
    			$permissions = $tblGroups->fetchAll($select)->toArray();
    		}
    			
    		if($privileges != null && $groups != null){
    			$tabs = '<ul class="nav nav-tabs">';
    			$contents = '<div class="tab-content">';
    			foreach ($groups as $index => $group){
    				$tabs .= '	<li' . ($index == 0 ? ' class="active"' : '') . '>
    						<a href="#' . $name . '_group_' . $group['id'] . '" data-toggle="tab">' . str_repeat('|—', $group['level']) . ' ' . $group['title'] . '</a>
    					</li>';
    				$contents .= '<div class="tab-pane' . ($index == 0 ? ' active' : '') . ' table-responsive" id="' . $name . '_group_' . $group['id'] . '">';
    				$contents .= '<table class="table table-striped table-hover">
		    				<thead>
			    				<tr>
				    				<th>Privilege</th>
				    				<th>Setting</th>
				    				<th>Calculated</th>
			    				</tr>
		    				</thead><tbody>';
    				foreach ($privileges as $privilegeKey => $privilege){
    					$setting = 'inhereit';
    					foreach ($permissions as $permission){
    						if($permission['id'] == $group['id'] && $permission['privilege'] == $privilegeKey){
    							$setting = $permission['assert'];
    						}
    					}
    					$calculator = $this->isAllowed('group_' . $group['id'], $resource, $privilegeKey) ? '<span class="text-success"><span class="glyphicon glyphicon-ok"></span> Allowed</span>' : '<span class="text-danger"><span class="glyphicon glyphicon-minus-sign"></span> Denied</span>';
    					$radio = new Zend_Form_Element_Radio($group['id'] . '_' . $privilegeKey, array(
    							'belongsTo' => $name,
    							'label' => 'Status:',
    							'value' => $setting,
    							'multiOptions' => array(
    									'inhereit' => 'Inherited',
    									'allow' => 'Allowed',
    									'deny' => 'Denied'
    							),
    							'label_class' => 'btn btn-default btn-xs',
    							'separator'   => '',
    							'decorators' =>  array(
    									'ViewHelper',
    									array(array('Value'=>'HtmlTag'), array('tag'=>'div','class'=>'btn-group btn-group-setting', 'data-toggle' => 'buttons', 'style' => 'min-width:180px;')),
    									array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;'))
    							)
    					));

    					$contents .= '	<tr>
				    				<td>' . $privilege['title'] . '</td>
				    				<td>' . $radio . '</td>
				    				<td>' . $calculator . '</td>
		    					</tr>';
    				}
    				$contents .= '</tbody></table>';
    				$contents .= '</div>';
    			}
    			$contents .= '</div>';
    			$tabs .= '</ul>';
    			$html = '<div class="tabbable tabs-left">' . $tabs . $contents . '</div>';
    
    			return $html;
    		}
    	}
    }
    
    public function moveResource($resource, $parent){
    	if (null !== $parent && null !== $resource) {
    		if($resource instanceof Zend_Acl_Resource_Interface){
    			$resourceId = $resource->getResourceId();
    		}else if (is_string($resource)) {
    			$resourceId = $resource;
    			$resource = $this->get($resource);
    		}
    
    		if($parent instanceof Zend_Acl_Resource_Interface){
    			$resourceParentId = $parent->getResourceId();
    		}else if (is_string($parent)) {
    			$resourceParentId = $parent;
    			$parent = $this->get($parent);
    		}
    
    		//old parent
    		$oldParent = $this->_resources[$resourceId]['parent'];
    		$oldParentId = $oldParent->getResourceId();
    		unset($this->_resources[$oldParentId]['children'][$resourceId]);
    
    		$this->_resources[$resourceParentId]['children'][$resourceId] = $resource;
    		$this->_resources[$resourceId]['parent'] = $parent;
    	}
    }
}