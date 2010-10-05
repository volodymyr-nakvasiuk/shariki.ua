<?php

/**
 * CarController
 *
 * @author
 * @version
 */

class Abstract_Controller_InitController extends Zend_Controller_Action {
	
	public static $currentAuth;
	protected $Session;
	protected $userData;
	protected $auth = false;
	
	protected $_actionId = false;
	
	/**
	 * Redirector - defined for code completion
	 *
	 * @var Zend_Controller_Action_Helper_Redirector
	 */
	protected $_redirector = null;
	
	/**
	 * Zend_Controller_Request_Http object wrapping the request environment
	 * @var Zend_Controller_Request_Http
	 */
	protected $_request = null;
	
	public function __call($f, $a){}
	
	public function init(){
		$uri = $this->_request->getRequestUri();
		if (strpos($uri, '//')){
			$count = 1;
			while ($count) $uri = str_replace("//", "/", $uri, $count);
			$this->_redirect($uri);
		}
		parent::init();
		$this->_redirector = $this->_helper->getHelper('Redirector');
		
		$this->initSession();
		$this->initViewParams();
		//ArOn_Core_Debug::getGenerateTime('params');
		$this->initActionId();
		//ArOn_Core_Debug::getGenerateTime('action');
	}
	
	protected function initSession(){
		if (!Zend_Session::isStarted()){
			Zend_Session::start();
			//$this->Session = new Zend_Session_Namespace('user');
		}
		$this->Session = Zend_Registry::get('defSession');
		$this->Session->setRequest($this->_request);
	}
	
	protected function initViewParams(){
		$this->view->module = $this->_request->getModuleName();
		$this->view->controller = $this->_request->getControllerName();
		$this->view->action = $this->_request->getActionName();
		$this->view->fullAction =   $this->_request->getModuleName().' - '.
									$this->_request->getControllerName().' - '.
									$this->_request->getActionName();
		$this->view->http_host = 'http://'.str_replace('forum.','',$_SERVER["HTTP_HOST"]);
		$this->view->fullUrl = $this->view->http_host.$this->_request->getRequestUri();
		$this->view->doctype('XHTML1_STRICT');
	}
	
	protected function initActionId(){
		$extra = '';
		$extraAction = array(
		);
		$uri = explode("?",$this->_request->getRequestUri());
		$matches = explode('/', $uri[0]);
		if (isset($matches[3]) && in_array($matches[3], $extraAction)!==false){
			$extra = '/'.$matches[3];
		}
		$this->view->fullAction .= $extra;
		$params = array(
					'modulename'=>$this->view->module,
					'controllername'=>$this->view->controller,
					'actionname'=>$this->view->action.$extra,
				);
		
		$action = Crud_Grid_ExtJs_SiteActs::getInstance(null, $params);
		$action->setNotCountQuery();
		$data = $action->getData();
		
		if (!empty($data['data'])){
			$this->_actionId = $data['data'][0]['id'];
		}
		elseif ($this->view->module == 'default'){
			$this->_forward('error', 'error');
			//$this->_request->setControllerName('error');
			//$this->_request->setActionName('error');
			return;
		}
	}
	
	public function phpinfoAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		phpinfo();
		die;
	}

	protected function redirectZend($action, $controller = null, $module = null, $params = array())
	{
	
		// Redirect to 'my-action' of 'my-controller' in the current
		// module, using the params param1 => test and param2 => test2
		$this->_redirector->gotoSimple( $action,
										$controller,
										$module,
										$params
									);

		return; // never reached
	}
	
	protected function redirectElse(){
		
		 /* do some stuff */

		// Redirect to a previously registered URL, and force an exit
		// to occur when done:
		$this->_redirector->redirectAndExit();
		
		 $this->_redirector
			->gotoUrl('/my-controller/my-action/param1/test/param2/test2');

		 // Redirect to blog archive. Builds the following URL:
		// /blog/2006/4/24/42
		$this->_redirector->gotoRoute(
			array('year' => 2006,
				  'month' => 4,
				  'day' => 24,
				  'id' => 42),
			'blogArchive'
		);
	}
}