<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
    
defined('PUBLISH_PATH')
    || define('PUBLISH_PATH', realpath(dirname(__FILE__)));

    /** Zend_Registry */
	// Check Location Site or Admin
    $filename     = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    $requestUri     = str_replace($filename, '', $_SERVER['REQUEST_URI']);
    $requestUriPath = explode('/', $requestUri);

    if(isset($requestUriPath[1]) && $requestUriPath[1] == 'administrator'){
    	$environment = 'administrator';
    }else{
    	$environment = 'production';
    }
    
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : $environment));
    
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
if($environment == 'administrator'){
	Zend_Registry::set('Zendvn_Location', 'admin');
}else{
	Zend_Registry::set('Zendvn_Location', 'site');
}
$application->bootstrap()->run();
