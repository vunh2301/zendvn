<?php

class ConfigController extends Zend_Controller_Action
{

    public function init()
    {
    	
    }

    public function indexAction()
    {
    	$acl 			= Zendvn_Factory::getAcl();
    	$form 			= new Admin_Form_Config();
    	$tblResource 	= new Zendvn_Db_Table_AclResource();
    	$globalConfig 	= new Zend_Config_Ini(
    			APPLICATION_PATH . '/configs/application.ini',
    			null,
    			array(
    					'skipExtends'        => true,
    					'allowModifications' => true
    			)
    	);
    	$options 		= array(
    			'site_name' 				=> $globalConfig->production->site->name,
    			'site_offline' 				=> $globalConfig->production->site->offline,
    			'site_offlineMessage' 		=> $globalConfig->production->site->offlineMessage,
    			'site_recordPerPage' 		=> $globalConfig->production->site->recordPerPage,
    			'site_route_urlRewrite' 	=> $globalConfig->production->site->route->urlRewrite,
    			'site_route_shortUrl' 		=> $globalConfig->production->site->route->shortUrl,
    			'site_route_suffix' 		=> $globalConfig->production->site->route->suffix,
    			'site_cookieDomain' 		=> $globalConfig->production->site->cookieDomain,
    			'site_cookiePath' 			=> $globalConfig->production->site->cookiePath,
    			
    			'site_meta_description' 	=> $globalConfig->production->site->metaDescription,
    			'site_meta_keywords' 		=> $globalConfig->production->site->metaKeywords,
    			'site_robots' 				=> $globalConfig->production->site->robots,
    			'site_contentRights' 		=> $globalConfig->production->site->contentRights,
    			
    			'displayExceptions' 		=> $globalConfig->production->resources->frontController->params->displayExceptions,
    			'site_timezone' 			=> $globalConfig->production->site->timezone,
    			
    			'resources_db_adapter' 				=> $globalConfig->production->resources->db->adapter ,
    			'resources_db_params_host' 			=> $globalConfig->production->resources->db->params->host,
    			'resources_db_params_username' 		=> $globalConfig->production->resources->db->params->username,
    			'resources_db_params_password' 		=> $globalConfig->production->resources->db->params->password,
    			'resources_db_params_dbname' 		=> $globalConfig->production->resources->db->params->dbname,
    			'resources_db_table_pfx' 			=> $globalConfig->production->resources->db->table_pfx,
    			
    			'resources_mail_transport_type' 	=> $globalConfig->production->resources->mail->transport->type,
    			'resources_mail_transport_host' 	=> $globalConfig->production->resources->mail->transport->host,
    			'resources_mail_transport_port' 	=> $globalConfig->production->resources->mail->transport->port,
    			'resources_mail_transport_auth' 	=> $globalConfig->production->resources->mail->transport->auth,
    			'resources_mail_transport_ssl' 		=> $globalConfig->production->resources->mail->transport->ssl,
    			'resources_mail_transport_username' => $globalConfig->production->resources->mail->transport->username,
    			'resources_mail_transport_password' => $globalConfig->production->resources->mail->transport->password,
    			'resources_mail_defaultFrom_email' 	=> $globalConfig->production->resources->mail->defaultFrom->email,
    			'resources_mail_defaultFrom_name' 	=> $globalConfig->production->resources->mail->defaultFrom->name,
    	);
    	$form->populate($options);
    	if($this->_request->isPost()){

    		$values = $form->getValues();
    		$globalConfig->production->site->name 				= $values['site_name'];
    		$globalConfig->production->site->offline  			= $values['site_offline'];
    		$globalConfig->production->site->offlineMessage  	= $values['site_offlineMessage'];
    		$globalConfig->production->site->recordPerPage  	= $values['site_recordPerPage'];
    		$globalConfig->production->site->route->urlRewrite  = $values['site_route_urlRewrite'];
    		$globalConfig->production->site->route->shortUrl  	= $values['site_route_shortUrl'];
    		$globalConfig->production->site->route->suffix  	= $values['site_route_suffix'];
    		$globalConfig->production->site->cookieDomain  		= $values['site_cookieDomain'];
    		$globalConfig->production->site->cookiePath  		= $values['site_cookiePath'];
    			
    		$globalConfig->production->site->metaDescription  	= $values['site_meta_description'];
    		$globalConfig->production->site->metaKeywords  		= $values['site_meta_keywords'];
    		$globalConfig->production->site->robots  			= $values['site_robots'];
    		$globalConfig->production->site->contentRights  	= $values['site_contentRights'];
    			
    		$globalConfig->production->resources->frontController->params->displayExceptions  = $values['displayExceptions'];
    		$globalConfig->production->site->timezone  = $values['site_timezone'];
    			
    		$globalConfig->production->resources->db->adapter   		= $values['resources_db_adapter'];
    		$globalConfig->production->resources->db->params->host  	= $values['resources_db_params_host'];
    		$globalConfig->production->resources->db->params->username  = $values['resources_db_params_username'];
    		$globalConfig->production->resources->db->params->password  = $values['resources_db_params_password'];
    		$globalConfig->production->resources->db->params->dbname  	= $values['resources_db_params_dbname'];
    		$globalConfig->production->resources->db->table_pfx  		= $values['resources_db_table_pfx'];
    			
   			$globalConfig->production->resources->mail->transport->type  		= $values['resources_mail_transport_type'];
   			$globalConfig->production->resources->mail->transport->host  		= $values['resources_mail_transport_host'];
   			$globalConfig->production->resources->mail->transport->port  		= $values['resources_mail_transport_port'];
   			$globalConfig->production->resources->mail->transport->auth  		= $values['resources_mail_transport_auth'];
    		$globalConfig->production->resources->mail->transport->ssl  		= $values['resources_mail_transport_ssl'];
    		$globalConfig->production->resources->mail->transport->username  	= $values['resources_mail_transport_username'];
    		$globalConfig->production->resources->mail->transport->password  	= $values['resources_mail_transport_password'];
    		$globalConfig->production->resources->mail->defaultFrom->email  	= $values['resources_mail_defaultFrom_email'];
    		$globalConfig->production->resources->mail->defaultFrom->name  		= $values['resources_mail_defaultFrom_name'];
    		 
    		$writer = new Zendvn_Config_Writer_Ini(
    				array(
    						'config'   => $globalConfig,
    						'filename' => APPLICATION_PATH . '/configs/application.ini'
    				)
    		);
    		$writer->write();
    		// Update Privileges Root resource
    		if($this->_request->getParam('rootPermission', null) !== null)
    			$tblResource->updatePrivileges('root', $this->_request->getParam('rootPermission', null));
    	}
    	 
    	$this->view->form = $form;
    	$this->view->permission = $acl->getForm('root', array(
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
		), 'rootPermission');
    }

}