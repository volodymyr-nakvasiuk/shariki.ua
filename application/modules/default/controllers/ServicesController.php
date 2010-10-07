<?php
class ServicesController extends Abstract_Controller_FrontendController {
	
	public function init(){
		parent::init();
		$this->view->activeMenu = 'services';
	}
	
	public function indexAction(){
		$this->_forward('detail');
	}
	
	public function detailAction(){
		$services = new Init_Services();
		$this->view->data = $services->getData();
		$id = $this->_request->getParam('id');
		$this->view->selectedId = $id?$id:$this->view->data['0']['services_id'];
		
		$this->view->layouts['left']["left_menu"] = array('inc/menu/services', 100);
	}

}