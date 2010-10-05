<?php

class ArOn_Zend_Cache_Backend_Static_Adapter
{

    protected $_cache = null;

    public function __construct(Zend_Cache_Core $cache)
    {
        $this->_cache = $cache;
    }
	
    public static function factory($frontend, $backend, $frontendOptions = array(), $backendOptions = array(), $customFrontendNaming = false, $customBackendNaming = false, $autoload = false)
    {
    	$cache = Zend_Cache::factory($frontend, $backend, $frontendOptions, $backendOptions, $customFrontendNaming, $customBackendNaming, $autoload);
    	return new ArOn_Zend_Cache_Backend_Static_Adapter($cache);
    }
    
    public function load($id)
    {
        $id = $this->_encodeId($id);
        $this->__call('load', array($id));
    }

    public function test($id)
    {
        $id = $this->_encodeId($id);
        $this->__call('test', array($id));
    }

    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $id = $this->_encodeId($id);
        $this->__call('save', array($data, $id, $tags, $specificLifetime));
    }

    public function remove($id)
    {
        $id = $this->_encodeId($id);
        $this->__call('remove', array($id));
    }

    public function __call($method, array $args)
    {
        return call_user_func_array(array($this->_cache, $method), $args);
    }

    public function removeRecursive($id) {
        $this->_cache->getBackend()->removeRecursive($id);
    }

    protected function _encodeId($id) {
        return bin2hex($id); // encode path to alphanumeric hexadecimal
    }
}