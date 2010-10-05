<?php
class ArOn_Cache_Backend_Tags implements ArOn_Cache_Backend_Interface {
	
	protected $_root;
	
	protected $_backend;
	protected $_options = array();
		
	public function __construct($backend,$table = 'Db_SiteCacheTags'){		
		$this->_setOptions($backend,$table);
		$this->setup();		
	}
	
	protected function setup(){
		$this->_initBackend();
	}
	
	protected function _initBackend(){
		$this->_backend = new ArOn_Zend_Cache_Backend_Tags($this->_options);
	}
	
	protected function _setOptions($backend,$table){
		if($backend instanceof Zend_Cache_Backend_Interface)
			$this->_options['backend'] = $backend;
		elseif($backend instanceof ArOn_Cache_Backend_Interface)
			$this->_options['backend'] = $backend->getBackend();
			
		$this->_options['table'] = $table;	
	}
	
	public function getBackend(){
		return $this->_backend;
	}
	
}