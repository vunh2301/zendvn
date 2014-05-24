<?php
class Zendvn_Config{
	public static function factory($path, $section = null, $options = false)
	{
		if(file_exists($path . '.ini')){
			//Zend_Debug::dump(new Zend_Config_Ini($path . '.ini', $section, $options));
			return new Zend_Config_Ini($path . '.ini', $section, $options);
		}else if(file_exists($path . '.json')){
			return new Zend_Config_Json($path . '.json', $section, $options);
		}else if(file_exists($path . '.xml')){
			return new Zend_Config_Xml($path . '.xml', $section, $options);
		}
		return null;
	}
}