<?php
class CalendarController extends Abstract_Controller_FrontendController {
	
	public function indexAction(){
		$date = $this->_request->getParam('d');
		$time = $date?strtotime($date):time();
		$date = date('Y-m-d', $time);
		$calendar = new Init_Calendar($date);
		$this->view->jsParams['date'] = $date;
		$this->view->date = ArOn_Crud_Tools_Date::russian_date('d F Y', $time);
		$this->view->data = $calendar->getData();
		$this->view->layouts['left']["left_calendar"] = array('inc/calendar/left', 100);
	}

	public function detailAction(){
		$id = $this->_request->getParam('id');
		if (!$id) $this->_forward('error', 'error');
		$db = new Db_Calendar();
		$r = $db->getRowById($id);
		if (!$r) $this->_forward('error', 'error');
		$time = strtotime($r['calendar_date']);
		$date = date('Y-m-d', $time);
		$calendar = new Init_Calendar($date);
		$this->view->jsParams['date'] = $date;
		$this->view->date = ArOn_Crud_Tools_Date::russian_date('d F Y', $time);
		$this->view->data = $calendar->getData();
		$this->view->selectedId = $id;
		$this->view->layouts['left']["left_calendar"] = array('inc/calendar/left', 90);
		$this->view->layouts['left']["left_menu"] = array('inc/menu/calendar', 110);
	}
}