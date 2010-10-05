<?php
class NewsController extends Abstract_Controller_FrontendController {
	
	public function indexAction(){
		$this->view->activeMenu = 'news';
		
		$team = new Init_News(3);
		$this->view->data = $team->getData();
	}
	
	public function archiveAction(){
		$this->view->activeMenu = 'news';
		
		$team = new Init_News('all', 'YEAR(news_created_date)='.date('Y'), 'ASC');
		$this->view->data = $team->getData();
	}

}