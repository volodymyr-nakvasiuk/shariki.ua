<?php
class CalendarController extends Abstract_Controller_FrontendController {
	
	public function indexAction(){
		$date = $this->_request->getParam('d');
		$time = $date?strtotime($date):time();
		$calendar = new Init_Calendar(date('Y-m-d', $time));
		$this->view->data = $calendar->getData();
		echo strval(floatval('2007-10-15'))=='2007-10-15';
		print_r($this->view->data);exit;
	}
	
	public function detailAction(){
		$news = new Init_Calendar();
		$this->view->data = $news->getData();
		$id = $this->_request->getParam('id');
		$this->view->selectedId = $id?$id:$this->view->data['0']['news_id'];
		
		$this->view->layouts['left']["left_menu"] = array('inc/menu/news', 100);
	}

}