<?php 
class Zendvn_Hook
{

    public static function getHooks($eventName = null)
    {
        if (is_null($eventName)) {
            return self::_getHooks();
        } else if (is_string ($eventName) && array_key_exists(strtolower($eventName), self::_getHooks())) {
            $hooks = self::_getHooks();
            return $hooks[strtolower($eventName)];
        }
 
        return null;
    }

    public static function addHook($eventName, $className, $methodName = null, $params = null)
    {
        if (is_string($eventName) && is_string($className)) {
            $lowerEventName = strtolower($eventName);
            $hook = array(
                'class' => $className, 
                'method' => $methodName,
                'params' => $params
            );
 
            $hooks = self::_getHooks();
            $hooks[$lowerEventName][] = $hook;
            self::_setHooks($hooks);
        }
    }
 
    public static function removeHook($eventName, $className, $methodName = null, $params = null)
    {
        if (is_string($eventName) && is_string($className)) {
 
            $lowerEventName = strtolower($eventName);
            $hooks = self::_getHooks();
            for ($i = 0; $i < count($hooks); $i++) {
                $hook = array(
                    'class' => $className, 
                    'method' => $methodName,
                    'params' => $params
                );
 
                if ($hooks[$lowerEventName][$i] === $hook) {
                    unset($hooks[$lowerEventName][$i]);
                }
            }
            // save to registry
            self::_setHooks($hooks);
        }
    }

    public static function clearAllHooks()
    {
        self::_setHooks(array());
    }

    public static function dispatchEvent($eventName, $value = null)
    {
        $lowerEventName = strtolower($eventName);
        $hooks = self::_getHooks();
        if (array_key_exists($lowerEventName, $hooks)) {
            // go through each hook and call it
            foreach ($hooks[$lowerEventName] as $hook) {
                $classname = $hook['class'];
                $methodname = $hook['method'];
                $hookParams = $hook['params'];
                $reflectionClass = new Zend_Reflection_Class($classname);
                // check method is set and exists in class
                if ($methodname !== null && $reflectionClass->hasMethod($methodname)) {
                    $reflectionMethod = $reflectionClass->getMethod($methodname);
                    $availableNumberOfParams = $reflectionMethod->getNumberOfParameters();
 
                    // check if method is static
                    if ($reflectionMethod->isStatic()) {
                        // call it (with parameters dependent on allowed number params of method)
                        if ($availableNumberOfParams == 0) { 
                            $reflectionMethod->invokeArgs(null);
                        } else if ($availableNumberOfParams == 1) { 
                            $reflectionMethod->invokeArgs(null, array($hookParams));
                        } else if ($availableNumberOfParams >= 2) { 
                            $reflectionMethod->invokeArgs(null, array($value, $hookParams));
                        }
                    } else {
                        // check if class is singleton and init class
                        if ($reflectionClass->hasMethod('getInstance')) {
                            $class = $reflectionClass->getMethod('getInstance')->invoke();
                        } else {
                            $class = new $classname();
                        }
                        // call it (with parameters dependent on allowed number params of method)
                        if ($availableNumberOfParams == 0) { 
                            $class->$methodname();
                        } else if ($availableNumberOfParams == 1) { 
                            $class->$methodname($hookParams);
                        } else if ($availableNumberOfParams >= 2) { 
                            $class->$methodname($value, $hookParams);
                        }
 
                    } 
                }
 
            }
        }
 
    }
 
    protected static function _getHooks()
    {
        if (Zend_Registry::isRegistered('Zendvn_Hook')) {
            return Zend_Registry::get('Zendvn_Hook');
        } else {
            Zend_Registry::set('Zendvn_Hook', array());
            return Zend_Registry::get('Zendvn_Hook');
        }
    }
 
    protected static function _setHooks($value)
    {
        Zend_Registry::set('Zendvn_Hook', $value);
    }
 
}
?>