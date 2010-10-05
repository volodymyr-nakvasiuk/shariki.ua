<?php
class ArOn_Zend_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract {
	
	protected $user = null;
	
	protected $module;
	protected $controller;
	protected $action;
	
	protected $role;
	protected $resource;
	protected $privilege;
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	if(!$this->_checkIp())
    		return;
    	$this->_initRequest();
    	$this->_initUser();
    	$this->_initRole();
    	$this->_switchModule();
    	$this->_switchController();
    	$this->_switchAction();
    	$error = $this->_checkAclRules();
    	
		if ($error !== false) {
			//Zend_Layout::getMvcInstance()->getView()->error = $error;
			if($this->_request->isXmlHttpRequest()){
				$this->_request->setControllerName('ajax');
				$this->_request->setActionName('empty');
			}elseif($this->module == 'default'){
				$this->_request->setControllerName('error');
				$this->_request->setActionName('error');
			}
			else{
				$this->_request->setControllerName('login');
				$this->_request->setActionName('login');
			}
			//$request->setDispatched(false);
		}
 
    }
    
 	protected function _initUser(){
    	switch($this->module){
    		case 'cms':
    			$this->user = ArOn_Acl_Auth_Admin_Abstract::getInstance()->getIdentity();    			
    			break;
    		case 'client':
    			$this->user = ArOn_Acl_Auth_Client_Abstract::getInstance()->getIdentity();
    			break;    			
    		default:
    			$this->user = array('user' => array('id' => 2, 'role' => 2) ); 			
    			break;
    	}
    	if(empty($this->user)) $this->user = array('user' => array('id' => 2, 'role' => 2) ); 
    }
    
    protected function _initRole(){
    	$this->role = $this->user['user']['role'];
    	
    	if($this->role == 4){
    		$this->module = 'moderator';
    	}
    	if($this->role == 5){
    		$this->module = 'rewrite';
    	}
    }
    
    protected function _switchModule(){
    	switch($this->module){
    		case 'cms':  			
    			$this->privilege = $this->_request->getParam('grid_action');
    			if(empty($this->privilege)) $this->privilege = $this->action;
    			if($this->controller == 'json') $this->privilege = 'index';
    			break;
    		case 'client':
    			$this->privilege = $this->action;
    			if(empty($this->privilege)) $this->privilege = 'empty';
    			//if($this->controller == 'json') $this->privilege = 'index';
    			break;
    		case 'moderator':
    		case 'rewrite':
    			$this->privilege = $this->_request->getParam('grid_action');
    			if(empty($this->privilege)) $this->privilege = $this->action;
    			else 						$this->privilege = $this->action."_".$this->privilege;   			
    			if($this->controller == 'json') $this->privilege = 'index';    			
    			break;   		
    		default:
    			$this->privilege = $this->action;    			
    			break;
    	}
    }
    
    protected function _switchController(){
    	switch ($this->controller){
    		case 'search':
    			$this->privilege = 'index';
    			break;
    		case 'phpinfo':
    			$this->action = 'phpinfo';
    			break;
    	}
    }
    
	protected function _switchAction(){
    	switch ($this->action){
    		case 'phpinfo':
    			$this->_request->setControllerName('index');
				$this->_request->setActionName('phpinfo');
				$this->controller = 'index'; 
				$this->privilege = 'index';
    			break;
    		case 'registration':
    			if($this->controller == 'news'){
    				$this->_request->setControllerName('registration');
					$this->_request->setActionName($this->_request->getParam('id'));
					$this->controller = 'registration'; 
					$this->privilege = $this->_request->getParam('id');
    			}
    			break;
    	}
    }
    
    protected function _checkAclRules(){
    	$error = false;
    	/**
    	 * @var ArOn_Acl_Controlv2
    	 */
    	$acl = ArOn_Acl_Controlv2::getInstance();
		if (!$acl->hasRole($this->role)) {
	  	    $error = "Sorry, the requested user role '".$this->role."' does not exist";									
	  	}
	  	if ($error === false && !$acl->has($this->module.'_'.$this->controller)) {
			$error = "Sorry, the requested controller '".$this->controller."' does not exist as an ACL resource";
 		}
 		//
		if ($error === false && !$acl->isAllowed($this->role, $this->module.'_'.$this->controller, $this->privilege)) {
			$error = "Sorry, the page you requested does not exist or you do not have access";
		}
		return $error;
    }
    
    protected function _checkIp(){
    	$black = CACHE_FILE_PATH ."/blackip.txt";
		$ipCheck = new ArOn_Crud_Tools_IpCheck($this->_request,$black,false);
		if($ipCheck->exists()){
			$request->setModuleName('default');
			$request->setControllerName('construction');
			$request->setActionName('index');
			return false;
		}
		return true;
    }
	
    protected function _initRequest(){
		$this->module = $this->_request->getModuleName();
    	$this->controller = $this->_request->getControllerName();
    	$this->action = $this->_request->getActionName();
    	
    	ArOn_Crud_Grid::$ajaxModuleName = $this->module;
    	ArOn_Crud_Form::$ajaxModuleName = $this->module;
    	ArOn_Crud_Tree::$ajaxModuleName = $this->module;
    }
    
}