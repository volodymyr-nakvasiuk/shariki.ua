<?php
class ArOn_Db_TableCache{  

	private $object;
  	private $cache;
  	private $root = '/data/cache/db_table/';
  
  	public function __construct($object,$root = false, $backendName = 'Apc') {
	    
	    $frontendName = 'Class';
	 	if($root !== false){
	 		$this->root = $root;
	 	}
	    $frontendOptions = array(
	      'lifetime' => 1800,
	    );
	 
	    $backendOptions = array(
	      'cache_dir' => $this->root
	    );
	 
	    $this->object = $object;
	    $frontendOptions['cached_entity'] = $object;
	 
	    try {
	      Zend_Loader::loadClass('Zend_Cache');	   
	      	$this->cache = ($backendName == 'File' ) 
	      		? Zend_Cache::factory($frontendName, $backendName, $frontendOptions,$backendOptions) 
	      		: Zend_Cache::factory($frontendName, $backendName, $frontendOptions);     
	    } catch(Exception $e) {
	      throw($e);
	    }
	  }
  
	public function __call($method, $args) {
	   $class = get_class($this->object);
	   $class_methods = get_class_methods($class);
	 
	   if(in_array($method , $class_methods)) {
	       $caller = Array($this->cache, $method);
	       return call_user_func_array($caller, $args);
	   }
	 
	   throw new Exception( " Метод " . $method . " не существует в классе " . get_class($class ) . "." );
  }
}