<?php
class TreeController extends Abstract_Controller_FrontendController {
	
	public function indexAction(){
		$market = new Init_Market();
		$this->view->market = $market->getCatData();
	}

}