<?php
class ArOn_Cache_Type_Grid extends ArOn_Cache_Type_Table{
  
	protected $_model = 'Db_View_SiteCacheForm';
	
	protected $_cacheDir = '/data/cache/crud/form';

	
	protected function _addNewCacheData(){
		$data = array(		'cache_type' => 'grid',
							'cache_pattern' => $this->_subject ,
							'cache_pattern_type' => 'eq' ,
							'cache_backend_type' => self::$tableBackhend ,
							'cache_lifetime'  => 1800,
							'cache_description' => 'Объекты ArOn_Crud_Form: '.$this->_subject );
		$this->_model->insert($data);
		$this->_cache_data = array();
		$this->_cache_data [] = $data;
	}
  	
	protected function _setFrontendOptions(){
		
		$cached_methods = array('_fetch');
		$non_cached_methods = array('setData','clearData','loadData','getRenderData','saveData','preSave','postSave','saveValidData','update');
         // Логгер кэша
		$cache_logger = new Zend_Log();		
		$cache_logger->addWriter( new Zend_Log_Writer_Stream( self::$root . '/data/cache/form.log' ) );
	    
	    $options = array(
			'caching'					=>	true,
			'cache_id_prefix'			=>	'ArOn_Crud_Form',
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
}