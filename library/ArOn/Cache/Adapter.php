<?php
class ArOn_Cache_Adapter{
	
	protected $_objectType;
	protected $_object;
	
	/**
     * Cache type object.
     *
     * @var 
     */
	protected $_cacher;
	
	/**
	 * Object that return after init cache
	 * 
	 * @var unknown_type
	 */
	protected $_cacheObject;
	
	const TABLE = 'Table';
	const DB 	= "Db";
	const VIEW 	= "View";
	const PAGE 	= "Page";
	const GRID  = 'Grid';
	const FORM 	= "Form";
	
	/**
	 * 
	 * @var Zend_Controller_Request_Http
	 */
	protected static $_request;
	
	protected $_exeptions = array();
	protected $_exeptionTypes = array('request');
	
	public function __construct(){
		$this->setup();
	}
	
	
	protected function setup(){
		
		$this->setExeptions(array(
									'request' => 
											array('module' => array('cms','client'))
									)
							);
		if($this->checkExeptions())
			$this->_setCacheAdapter();
	}
	
	public function getCacher($object = null){
		$this->_setObject($object);
		$this->_setObjectType();
		$this->_initCache();
		return $this->getCacheObject();
	}
	
	public function setExeptions(array $exeptions){
		foreach($exeptions as $type => $exeption){
			if(!in_array($type, $this->_exeptionTypes))
				continue;			
			$this->_exeptions [$type] = $exeption;	
		}
		 
	}
	
	public function checkExeptions(){
		if(empty($this->_exeptions))
			return true;
		$result = true;
		foreach ($this->_exeptions as $type => $exeption){
			if($type == 'request'){
				$result = $this->checkRequest($exeption);
			}
		}
		return $result;
	}
	
	public function checkRequest($rules){
		self::setRequest();
		if(!is_array($rules))
			$rules = array('regexp' => $rules);
		foreach($rules as $type => $exp){
			if(!is_array($exp))
				$exp = array($exp);
			if($this->_checkRequestRule($type,$exp) === false)
				return false;			
		}
		return true;
	}
	
	protected function _setCacheAdapter(){	
		ArOn_Db_Table::setCacheAdapter($this);
		//ArOn_Crud_Grid::setCacheAdapter($this);
		//ArOn_Crud_Form::setCacheAdapter($this);
	}
	
	protected function _initCache(){
		$classCacheTypeName = $this->getCacheTypeName();
		$this->_cacher = new $classCacheTypeName ( $this->_object );
		$this->_cacheObject = $this->_cacher->getObject();
	}
	
	protected function _setObjectType(){
		if($this->_object instanceof ArOn_Db_Table){
			$this->_objectType = self::TABLE;
		}elseif($this->_object instanceof Zend_Db_Adapter_Abstract){
			$this->_objectType = self::DB;
		}elseif($this->_object instanceof ArOn_Crud_Grid){
			$this->_objectType = self::GRID;
		}elseif($this->_object instanceof ArOn_Crud_Form){
			$this->_objectType = self::FORM;
		}elseif($this->_object instanceof ArOn_Zend_View){
			$this->_objectType = self::VIEW;
		}else{
			$this->_objectType = self::PAGE;
		}
	}
	
	protected function _setObject($object = false){
		$this->_object = $object;
	}
	
	public function getCacheTypeName(){
		return 'ArOn_Cache_Type_' . $this->_objectType;
	}
	
	public function getCacheObject(){
		return $this->_cacheObject;
	}
	
	public static function setRequest(){
		$uri = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		$path = explode('/',$_SERVER["REQUEST_URI"]);

		$module = (array_key_exists(1,$path)) ? $path [1] : "default";
		$controller = (array_key_exists(2,$path)) ? $path [2] : "index";
		$action = (array_key_exists(3,$path)) ? $path [4] : "index";
		self::$_request = new Zend_Controller_Request_Http($uri);
		self::$_request->setModuleName($module);
		self::$_request->setControllerName($controller);
		self::$_request->setActionName($action);
	}
	
	protected function _checkRequestRule($type,$exeptions){
		foreach ($exeptions as $pattern){
			if($type == 'regexp'){
				$subject = self::$_request->getRequestUri();
				if(preg_match("`$pattern`", $subject))
					return false;
			}elseif($type == 'module'){
				$subject = self::$_request->getModuleName();
				if($pattern == $subject)
					return false;
			}elseif($type == 'controller'){
				$subject = self::$_request->getControllerName();
				if($pattern == $subject)
					return false;
			}elseif($type == 'action'){
				$subject = self::$_request->getActionName();
				if($pattern == $subject)
					return false;
			}
		}
		return true;
	}
}