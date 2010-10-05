<?php
class ArOn_Cache_Type_Page extends ArOn_Cache_Type_Abstract{
	
	protected $_model = 'Db_View_SiteCachePage';
	protected $_frontend = 'Page';
	
	protected $_cacheDir = '/data/cache/page';
	
	public function __construct(){
		parent::__construct(null);
	}
	
	protected function _setSubject(){
		$this->_subject = $_SERVER['REQUEST_URI'];
	}
		
	public function start(){
		if($this->_id === false) return false;		
		$this->_setCacher();
		return true;
	}
	
	protected function _setFrontendOptions(){
        $frontendOptions = array(
            // cache for 1 hour
            'lifetime' => $this->_options ['cache_lifetime'],
            // Disable caching by default for all URLs
            'default_options' => array(
        		'cache_with_cookie_variables' => true,
        		'cache_with_session_variables' => true,
                'cache' => false
            ),
            // Only cache URLs for Index and News controllers
            // matching the following patterns
            'regexps' => array(
                $this->_options ['cache_pattern'] => array('cache' => true)
            )
        );
        
        $this->_frontendOptions = $frontendOptions;
	}
	
	protected function _setCacher()
    {    	
        parent::_setCacher();      
        $this->_cache->start();
    }    
    
	public function getCacheCoreClassName(){
		
		if($this->_getBackendPrefix() == self::HTML){
			$core = 'ArOn_Zend_Cache_Backend_Static_Adapter';
		}else{
			$core = 'Zend_Cache';
		}
		return $core;
	}
	
}