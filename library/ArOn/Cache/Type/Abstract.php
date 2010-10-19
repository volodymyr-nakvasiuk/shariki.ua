<?php
class ArOn_Cache_Type_Abstract implements ArOn_Cache_Type_Interface {
	
	const APC   = 'apc';
	const EAC   = 'eaccelerator';
	const HTML  = "html";
	const MEMCH = "memcached";
	const FILE  = "file";
	
	/**
	 * @var $_cache Zend_Cache_Core
	 */
	protected $_cache;
	
	protected $_object;
	
	protected $_model = 'Db_SiteCache';
	protected $_cache_data = array();
	protected $_subject;
	protected $_id;
	
	protected $_options;
	
	protected $_frontend;
	protected $_frontendOptions;
	protected $_backend;
	
	protected $_useTagBackend = true;
	protected $_customFrontendNaming = true;
	protected $_customFrontendAutoload = true;
	
	public static $backend = false;
	public static $root;
	
	protected $_cacheDir = '/data/cache';
	
	public function __construct($object) {
    	$this->_object = $object;
		$this->setup();
		$this->init();
	}
	
	public function init(){}
	
	public function getObject(){
		return $this;
	}
	
	protected function setup(){
		$this->_initModel();
		$this->_setSubject();
		$this->_setCacheData();
		$this->_initCache();
	}
		
	protected function _initModel(){
		//$this->_model = ArOn_Crud_Tools_Registry::singleton($this->_model);
		$model = new $this->_model;
		$dir = self::$root . '/data/cache/db_table';
		$this->_model = new ArOn_Db_TableCache($model,$dir, 'File');
	}
	
	protected function _setCacheData(){
		$result = $this->_model->fetchAll();
		if(!empty($result))
			$this->_cache_data = $result->toArray();
	}
	
	protected function _setSubject(){
		$this->_subject = get_class($this->_object);
	}
	
	protected function _initCache(){
		if($this->_findSubjectInCache() === false){
			return false;
		}		
		$this->_setOptions();
		$this->_setFrontendOptions();
		$this->_setBackend();
		$this->_setCacher();
	}
	
	protected function _findSubjectInCache(){
		$lastKey = false;
		$lastMatchingRegexp = false;
		$subject = $this->_getSearchSubject();
		foreach ($this->_cache_data as $key => $conf) {
			$pattern = $conf ['cache_pattern'];
            if ($this->_compare($conf ['cache_pattern_type'],$pattern,$subject)) {
                $lastMatchingRegexp = $pattern;
                $lastKey = $key;
            }
        }
        $this->_id = $lastKey;
        return $lastMatchingRegexp;
	}
	
	protected function _setCacher()
    {	
        $className = $this->getCacheCoreClassName();
		$fn = $className . '::' . 'factory';
		$this->_cache = call_user_func($fn,
			$this->_frontend,
            $this->_backend,
            $this->_frontendOptions,
            array(),
            $this->_customFrontendNaming,
            $this->_customFrontendAutoload
        );
    } 
	
	protected function _compare($type,$pattern,$subject){
		if($type == 'eq'){
			return $this->_eq($pattern,$subject);
		}elseif($type == 'regexp'){
			return $this->_regexp($pattern,$subject);
		}
		return false;
	}
	
	protected function _regexp($pattern,$subject){
		if(preg_match("`$pattern`", $subject))
			return true;
		return false;
	}
	
	protected function _eq($pattern,$subject){
		if($pattern == $subject)
			return true;
		return false;
	}
	
	protected function _getSearchSubject(){
		return $this->_subject;
	}
	
	protected function _setOptions(){
		$this->_options = $this->_cache_data [$this->_id];		
	}
	
	public function getCacheCoreClassName(){
		
		return 'Zend_Cache';
	}
	
	protected function _setBackend(){
		if($this->_getBackendPrefix() == self::HTML){
			$this->_cacheDir = '';
		}
    	$backend = $this->getBackendClassName();
    	$backend = new $backend (self::$root . $this->_cacheDir);
    	if($this->useTagBackend())
    		$backend = new ArOn_Cache_Backend_Tags ($backend);
    	$this->_backend = $backend->getBackend();
    }
    
	public function getBackendClassName(){
		$prefix = $this->_getBackendPrefix();
		$prefix = ucfirst($prefix);
		return 'ArOn_Cache_Backend_' . $prefix;
	}
	
	protected function _getBackendPrefix(){
		return (self::$backend) ? self::$backend : $this->_options ['cache_backend_type'] ;
	}
	
	 /**
     * Get tag flag
     * 
     * @return bool
     */
	public function useTagBackend(){
		return $this->_useTagBackend;
	}
}