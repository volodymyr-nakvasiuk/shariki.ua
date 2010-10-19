<?php
class NewsController extends Abstract_Controller_FrontendController {
	
	public function init(){
		parent::init();
		$this->view->activeMenu = 'news';
	}
	
	public function indexAction(){
		$news = new Init_News(3);
		$this->view->data = $news->getData();
	}
	
	public function archiveAction(){
		$news = new Init_News('all', 'YEAR(news_created_date)='.date('Y'), 'ASC');
		$this->view->data = $news->getData();
	}
	
	public function detailAction(){
		$news = new Init_News();
		$this->view->data = $news->getData();
		$id = $this->_request->getParam('id');
		$this->view->selectedId = $id?$id:$this->view->data['0']['news_id'];

		$gallery = new Init_Gallery_News($this->view->selectedId);
		$this->view->gallery = $gallery->getData();
		//echo '<pre>';print_r($this->view->gallery);exit;
		
		$this->view->layouts['left']["left_menu"] = array('inc/menu/news', 100);
	}

}