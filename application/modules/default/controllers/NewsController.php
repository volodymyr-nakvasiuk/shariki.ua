<?php
class NewsController extends Abstract_Controller_FrontendController {
	
	public function indexAction(){
		$this->view->activeMenu = 'news';
		
		$team = new Init_News(3);
		$this->view->data = $team->getData();
	}

}