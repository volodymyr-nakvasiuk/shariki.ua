<?php
class ArOn_Cache_Backend_Apc implements ArOn_Cache_Backend_Interface {
	
	protected $_root;
	
	protected $_backend;
		
	public function __construct($root){
		$this->_root = $root;
		$this->setup();		
	}
	
	protected function setup(){
		$this->_initBackend();
	}
	
	protected function _initBackend(){
		$this->_backend = new Zend_Cache_Backend_Apc();
		//$this->_backend = new ArOn_Zend_Cache_Backend_Apc();
	}
	
	public function getBackend(){
		return $this->_backend;
	}
	
}