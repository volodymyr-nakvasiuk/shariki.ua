<?php
class Client_ClientController extends Client_AjaxController  {
	
	public $resultJSON = array();
	
	public function carsAction() {
		$this->view->mark_caches = Zend_Registry::get ('marks');
		$this->view->models_caches = Zend_Registry::get ('models');
		$this->view->currency = Zend_Registry::get ('currency');
		$this->view->regions = Zend_Registry::get ('regions');
		$this->view->transmissions = Zend_Registry::get ('article_transmission_type');
		$this->view->fuel = Zend_Registry::get ('fuel');
		$this->view->body_type = Zend_Registry::get ('article_body_type');
		$this->view->drive = Zend_Registry::get ('drive');
		$this->view->colors = Zend_Registry::get ('colors');
	}
	
	public function preDispatch() {
		parent::preDispatch();
		$this->resultJSON['success'] = true;
	}
	
	public function postDispatch() {
		$action = $this->_request->getActionName();
		$this->resultJSON['content'] = $this->view->render("client/".$action.".phtml")
										.'<script type="text/javascript">'
										.$this->view->run(DOCUMENT_ROOT."/js/inc/client/".$action.".js")
										.'</script>';
		$this->resultJSON['style'] = $this->view->run(DOCUMENT_ROOT."/css/inc/client/".$action.".css");
		parent::postDispatch();
	}
}
