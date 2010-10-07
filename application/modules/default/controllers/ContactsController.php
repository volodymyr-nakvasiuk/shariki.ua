<?php
class ContactsController extends Abstract_Controller_FrontendController {
	
	public function init(){
		parent::init();
		$this->view->activeMenu = 'contacts';
	}
	
	public function indexAction(){
		$static = new Init_Static($this->_actionId);
		$this->view->staticContent = $static->getData();
	}

}