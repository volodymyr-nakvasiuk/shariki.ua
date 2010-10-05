<?php
class GalleryController extends Abstract_Controller_FrontendController {
	
	public function indexAction(){
		$this->view->activeMenu = 'gallery';
		
		$photos = new Init_Photos();
		$this->view->data = $photos->getData();
	}

}