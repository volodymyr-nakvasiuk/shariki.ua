<?php
class ArOn_Crud_Cache{  

	/**
	 * 
	 * @var ArOn_Cache_Adapter
	 */
	protected static $_cacheAdapter;
	protected static $_cache = false;
	
	protected static $_instance = array();
	
	static function getInstance(){
		$className = get_called_class();
	    if (array_key_exists($className,self::$_instance) === false)
	    {	 //var_dump($className);die;	    	
	    	if(!class_exists($className)) {
	    		throw new Zend_Controller_Exception("Class " . $className ." does not exists!");
	    	}
	    	$class = new $className ('cache');	    	
	    	$args = func_get_args();
			$caller = array($class, '__construct');
	     	call_user_func_array($caller, $args);
		    if(self::$_cache === true){   	
		    	self::$_instance [$className] = self::$_cacheAdapter->getCacher($class);
		    }else{
		    	self::$_instance [$className] = $class;
		    }
	    }
		
	    return self::$_instance [$className];
	}
	
	public static function setCacheAdapter(ArOn_Cache_Adapter $adapter){
		self::$_cache = true;
		self::$_cacheAdapter = $adapter;
		return true;
	}
}