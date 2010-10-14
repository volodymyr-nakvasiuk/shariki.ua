<?php
class CalendarController extends Abstract_Controller_FrontendController {
	
	public function indexAction(){
		$date = $this->_request->getParam('d');
		$time = $date?strtotime($date):time();
		$calendar = new Init_Calendar(date('Y-m-d', $time));
		$this->view->data = $calendar->getData();
	}
	/*
	public function detailAction(){
		$calendar = new Init_Calendar();
		$this->view->data = $calendar->getData();
		$id = $this->_request->getParam('id');
		$this->view->selectedId = $id?$id:$this->view->data['0']['news_id'];
		
		$this->view->layouts['left']["left_menu"] = array('inc/menu/news', 100);
	}
	*/
}