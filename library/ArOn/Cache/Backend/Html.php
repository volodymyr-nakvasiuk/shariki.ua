<?php
class ArOn_Cache_Backend_Html implements ArOn_Cache_Backend_Interface {
	
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
		$this->_backend = new ArOn_Zend_Cache_Backend_Static($this->_options);
	}
	
	protected function _setOptions(){
		$this->_options = array(
            'debug_header' => false,
            // cache to a sub-directory of /public for separation
            'public_dir' => $this->_root . '/www/html'
        );
	}
	
	public function getBackend(){
		return $this->_backend;
	}
	
}