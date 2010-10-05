<?php
class Client_AjaxController extends Abstract_Controller_ClientController {
	
	public $resultJSON = false;

	public function init(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		parent::init();
	}
	
	protected function setup(){
		$this->initUser();
		$this->initCache();
	}
	
	public function preDispatch() {}
	
	public function postDispatch() {
		if ($this->resultJSON !== false){
			$this->_response->setHeader('Content-type', 'application/json');
			echo Zend_Json_Encoder::encode($this->resultJSON);
		}
		if (!$this->_request->isXmlHttpRequest() && !$this->_request->getParam('PHPSESSID',false)) $this->_redirect('/client/');
	}
	
	public function emptyAction(){
		$this->resultJSON['success'] = false;
		$this->resultJSON['message'] = 'У вас нет доступа к запрашиваемому разделу.<br/>Возможо повторный вход в систему решит проблему!';
	}
}
