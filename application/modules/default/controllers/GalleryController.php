<?php
class GalleryController extends Abstract_Controller_FrontendController {
	
	public function init(){
		parent::init();
		$this->view->activeMenu = 'gallery';
	}
	
	public function indexAction(){
		$photos = new Init_Photos();
		$this->view->data = $photos->getData();
	}

}