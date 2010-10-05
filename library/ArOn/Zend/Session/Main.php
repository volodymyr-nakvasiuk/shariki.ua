<?php
class ArOn_Zend_Session_Main extends Zend_Session_Namespace {
	
	/**
	 * @var Zend_Controller_Request_Http 
	 */
	protected $_request;
	
	public function __construct($namespace = 'Main', $singleInstance = false){
		
		parent::__construct($namespace,$singleInstance);
		$this->setup();
	}
	
	protected function setup(){
		
		$this->_setWalking();
	}
	
	protected function _setWalking(){
		if(empty($this->walking))
			$this->walking = array();
		if(!array_key_exists('count', $this->walking))
			$this->walking ['count'] = 0;
		$this->walking ['count']++;
		if(!array_key_exists('pages', $this->walking))
			$this->walking ['pages'] = array();
		return $this;
	}
	
	public function setRequest(Zend_Controller_Request_Http $request){
		$this->_request = $request;
		$this->_setVisitPageFormRequest();
		return $this;
	}
	
	protected function _setVisitPageFormRequest(){
		$this->visitPage($this->_request->getServer('REDIRECT_URL'),$this->_request->getServer('HTTP_REFERER'));
		return $this;
	}
	
	public function visitPage($page,$referer = false){
		
		$this->walking ['last'] = $page;
		$this->walking ['referer'] = $referer;
		
		if(!array_key_exists($page, $this->walking['pages']))
			$this->walking ['pages'][$page] = array('count' => 0);
		$this->walking ['pages'][$page]['count']++;
		$this->walking ['pages'][$page]['referer'] = $referer;
		
		return $this;
	}
	
	static function checkClientAgent(){
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$exeptions = array(
		'woodpckr',
		'Googlebot',
		'meta\.ua/spider',
		'Yandex/1\.01\.001',
		'help\.yahoo\.com/help/us/ysearch/slurp',
		'www\.cuil\.com/twiceler/robot'
		);
		
		foreach ($exeptions as $exp){
			$exp = str_replace ('\/','/',$exp);
			$exp = str_replace ('/','\/',$exp);
			if(preg_match("/".$exp."/i",$agent))
				return false;
		}
		return true;
	}
	
}
