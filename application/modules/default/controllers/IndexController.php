<?php
class IndexController extends Abstract_Controller_FrontendController {
	
	public function indexAction(){
		$indexmenu = new Init_Indexmenu();
		$this->view->subMenu = $indexmenu->getData();
		$this->view->layouts['left']["left_menu"] = array('inc/menu/submenu', 100);
		
		$news = new Init_News(3);
		$this->view->newsLeft = $news->getData();
		$this->view->layouts['left']["left_news"] = array('inc/news/left', 100);
		
		$partners = new Init_Partners();
		$this->view->partners = $partners->getData();
		$this->view->layouts['bottomRow']["partners_box"] = array('inc/partners', 100);
		
		$static = new Init_Static($this->_actionId);
		$this->view->staticContent = $static->getData();
		
	}

}