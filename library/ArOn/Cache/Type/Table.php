<?php
class ArOn_Cache_Type_Table extends ArOn_Cache_Type_Abstract{
  
	protected $_model = 'Db_View_SiteCacheTable';
	//protected $_frontend = 'Class';
	protected $_frontend = 'ArOn_Zend_Cache_Frontend_Class';
	
	public static $tableBackhend = ArOn_Cache_Type_Abstract::APC;
	
	protected $_cacheDir = '/data/cache/db_table';
	
	public function init(){
		if($this->_id === false){
			$this->_addNewCacheData();
			$this->_initCache();
		}
	}
	
	protected function _addNewCacheData(){
		$data = array(		'cache_type' => 'table',
							'cache_pattern' => $this->_subject ,
							'cache_pattern_type' => 'eq' ,
							'cache_backend_type' => self::$tableBackhend ,
							'cache_lifetime'  => 1800,
							'cache_autoclean'  => 0,
							'cache_description' => 'Таблицы ArOn_Db_Table: '.$this->_subject );
		$this->_model->insert($data);
		$this->_cache_data = array();
		$this->_cache_data [] = $data;
	}
	
	protected function _setSubject(){
		$this->_subject = get_class($this->_object);
	}
	
	protected function _setFrontendOptions(){
		
		$cached_methods = array('_fetch');
		$non_cached_methods = array('insert','update','delete','select','getPrimary','getTableName','getClass','getJoinPath','getNameExpr','applyAlias');
         // Логгер кэша
		$cache_logger = new Zend_Log();		
		$cache_logger->addWriter( new Zend_Log_Writer_Stream( self::$root . '/data/cache/db_table.log' ) );
	    
	    $options = array(
			'caching'					=>	true,
			'cache_id_prefix'			=>	'ArOn_Db_Table',
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
	      	'lifetime' =>  $this->_options ['cache_lifetime']
	    );
	 	
	    //$frontendOptions = array_merge($options, $frontendOptions);
        
        $this->_frontendOptions = $frontendOptions;
	}
  
	public function getObject(){
		if($this->_id === false){
			return $this->_object;
		}else{
			if($this->_options ['cache_status'] == 0){
				return $this->_object;
			}else{
				return $this;
			}				
		}
	}
  
	public function __call($method, $args) {
	   $non_cached_methods = array('insert','update','delete');
	   $class = get_class($this->_object);
	   $class_methods = get_class_methods($class);	   
	   if(in_array($method , $class_methods)) {
	   		if(in_array($method , $non_cached_methods) && ($this->_options['cache_autoclean'] == 1)){
		   		$this->_cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array($class));
		   	}
	   		$this->_cache->setTagsArray(array($class,$method));		   	
	        $caller = Array($this->_cache, $method);
	        return call_user_func_array($caller, $args);
	   }
	 
	   throw new Exception( " Метод " . $method . " не существует в классе " . $class . "." );
	}
}