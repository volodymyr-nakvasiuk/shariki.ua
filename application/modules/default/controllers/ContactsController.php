<?php
class ContactsController extends Abstract_Controller_FrontendController {
	
	public function indexAction(){
		$static = new Init_Static($this->_actionId);
		$this->view->staticContent = $static->getData();
		
	}

}