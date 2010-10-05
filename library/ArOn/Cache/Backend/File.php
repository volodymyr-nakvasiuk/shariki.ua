<?php
class ArOn_Cache_Backend_File implements ArOn_Cache_Backend_Interface {
	
	protected $_root;
	
	protected $_backend;
	protected $_options = array();
		
	public function __construct($root){
		$this->_root = $root;
		$this->setup();		
	}
	
	protected function setup(){
		$this->_setOptions();
		$this->_initBackend();
	}
	
	protected function _initBackend(){
		$this->_backend = new Zend_Cache_Backend_File($this->_options);
	}
	
	protected function _setOptions(){
		$this->_options = array(
            // cache to a sub-directory of /public for separation
            'cache_dir' => $this->_root,
			//'file_name_prefix' => 
        );
	}
	
	public function getBackend(){
		return $this->_backend;
	}
	
}