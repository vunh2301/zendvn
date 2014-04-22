<?php 
abstract class Zendvn_Object_Abstract implements ArrayAccess, IteratorAggregate
{
	protected $_properties = array();
	
	public function __construct(array $config = null)
	{
		if (is_array($config)) {
			$this->setProperties($config);
		}elseif($config instanceof Zend_Config){
			$this->setProperties($config->toArray());
		}
	
		$this->init();
	}
	
	public function setConfig(Zend_Config $config)
	{
		return $this->setProperties($config->toArray());
	}

	public function setProperties(array $properties)
	{
		foreach ($properties as $property => $value) {
			$this->set($property, $value);
		}
	
		return $this;
	}
	
	public function set($property, $value)
    {
        if (!is_string($property) || empty($property)) {
            throw new Exception('Invalid argument: $property must be a non-empty string');
        }
        
        $propertyName = str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));
        $method = 'set' . $propertyName;

        if ($method != 'setProperties' && $method != 'setConfig' && method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $this->_properties[$property] = $value;
        }

        return $this;
    }

    public function get($property)
    {
        if (!is_string($property) || empty($property)) {
            throw new Exception('Invalid argument: $property must be a non-empty string');
        }

        $propertyName = str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));
        $method = 'get' . $propertyName;

        if (method_exists($this, $method)) {
            return $this->$method();
        } elseif (isset($this->_properties[$property])) {
            return $this->_properties[$property];
        }

        return null;
    }
    
    public function __set($name, $value)
    {
    	$this->set($name, $value);
    }
    
    public function __get($name)
    {
    	return $this->get($name);
    }
	
	public function __unset($name)
	{
		$propertyName = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
		$method = 'set' . $propertyName;
		if (method_exists($this, $method)) {
			throw new Exception(sprintf('Unsetting native property "%s" is not allowed', $name));
		}
	
		if (isset($this->_properties[$name])) {
			unset($this->_properties[$name]);
		}
	}
	
	public function __isset($name)
    {
    	$propertyName = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        $method = 'get' . $propertyName;
        if (method_exists($this, $method)) {
            return true;
        }

        return isset($this->_properties[$name]);
    }
	
	public function offsetExists($offset)
	{
		return $this->__isset($offset);
	}
	
	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}
	
	public function offsetSet($offset, $value)
	{
		$this->__set($offset, $value);
	}
	
	public function offsetUnset($offset)
	{
		return $this->__unset($offset);
	}
	
	public function __Call($method, array $args)
	{
		$method = '';
		if(null !== ($properties = $this->_properties)){
			foreach ($properties as $property => $value){
				if($method == 'set' . ucfirst($property) && count($args) == 1){
					array_unshift($args, $property);
					return call_user_func_array(array($this, '__set'), $args);
				}
				if($method == 'get' . ucfirst($property) && count($args) == 0){
					return $this->__get($property);
					
				}
			}
		}
	
		throw new Exception('Invalid ' . $method . ' method');
	}
	
	public function getIterator()
	{
		return new ArrayIterator((array) $this->_properties);
	}

	public function toArray()
	{
		return $this->_properties;
	}
	
	public function init(){
	
	}
}
?>