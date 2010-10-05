<?php
class ArOn_Cache_Type_Db extends ArOn_Cache_Type_Abstract{

	protected $_model = 'Db_View_SiteCacheDb';
	protected $_frontend = 'ArOn_Zend_Cache_Frontend_Class';
	
	public static $tableBackhend = ArOn_Cache_Type_Abstract::APC;
	
	protected $_cacheDir = '/data/cache/db_table';
	
	public function init(){
		if($this->_id === false){
			//$this->_addNewCacheData();
			$this->_initCache();
		}
	}
	
	protected function _addNewCacheData(){
		$data = array(		
							'cache_type' => 'db',
							'cache_pattern' => $this->_subject ,
							'cache_pattern_type' => 'eq' ,
							'cache_backend_type' => self::$tableBackhend ,
							'cache_lifetime'  => 600,
							'cache_description' => 'Таблицы ArOn_Db_Table: '.$this->_subject );
		$this->_model->insert($data);
		$this->_cache_data = array();
		$this->_cache_data [] = $data;
	}
	
	protected function _setCacheData(){
		$this->_cache_data = array(1 => array(
						'cache_type' => 'db',
						'cache_pattern' => $this->_subject,
						'cache_pattern_type' => 'eq',
						'cache_backend_type' => self::$tableBackhend ,
						'cache_lifetime' => 600
						)
					);			
	}
	  	
	protected function _setFrontendOptions(){
		
		$cached_methods = array('_fetch');
		$non_cached_methods = array(
								'insert','update','delete','query',
								'limit','_quote','quote','quoteInto','_quoteIdentifierAs','quoteTableAs','quoteColumnAs','quoteIdentifier','getFetchMode'
								);
         // Логгер кэша
		$cache_logger = new Zend_Log();		
		$cache_logger->addWriter( new Zend_Log_Writer_Stream( self::$root . '/data/cache/db_adapter.log' ) );
	    
	    $options = array(
			'caching'					=>	true,
			'cache_id_prefix'			=>	'Zend_Db_Adapter',
			'logging'					=>	true,
			'logger'					=>	$cache_logger,
			'write_control'				=>	true,
			'automatic_serialization'	=>	true,
			'ignore_user_abort'			=>	true
		);
	    
	    
	    $frontendOptions = array(    	
	      	'cached_entity' => $this->_object,
	    	'cache_by_default' => true,
	    	//'cached_methods' => $cached_methods,
	    	'non_cached_methods' => $non_cached_methods,
	      	'lifetime' => $this->_options ['cache_lifetime']
	    );
	 	
	    //$frontendOptions = array_merge($options, $frontendOptions);
        
        $this->_frontendOptions = $frontendOptions;
	}
  
	public function getObject(){
	  //return $this->_object;
	  return ($this->_id !== false) ? $this : $this->_object;
	}
  
	public function __call($method, $args) {
	  	
	   $class = get_class($this->_object);
	   $class_methods = get_class_methods($class);
	   if(in_array($method , $class_methods)) {
	        $caller = Array($this->_cache, $method);
	        return call_user_func_array($caller, $args);
	   }
	 
	   throw new Exception( " Метод " . $method . " не существует в классе " . $class . "." );
	}
}