<?php
class Abstract_Controller_ClientController extends Abstract_Controller_FrontendController {
	
	public function init(){
		parent::init();
		
		if($this->auth){
			$client = new Init_Client($this->view, $this->Session);
			$client->initClientMode();
		}
	}
}