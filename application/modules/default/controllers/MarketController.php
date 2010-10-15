<?php
class MarketController extends Abstract_Controller_FrontendController {
	
	public function init(){
		parent::init();
		$this->view->activeMenu = 'market';
	}
	
	public function indexAction(){
		$static = new Init_Static($this->_actionId);
		$this->view->staticContent = $static->getData();

		$market = new Init_Market();
		$this->view->data = $market->getCatData();
	}
	
	public function categoryAction(){
		$id = $this->_request->getParam('id');
		$market = new Init_Market();
		$this->view->data = $market->getTovData($id);
	}
}