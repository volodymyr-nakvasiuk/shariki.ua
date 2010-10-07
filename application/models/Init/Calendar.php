<?php
class Init_Calendar {
	
	protected $_data = false;
	protected $_date = null;
	
	public function __construct($date){
		$this->_date = $date;
	}
	
	public function getData(){
		if (!$this->_data) $this->_setData();
		return $this->_data;
	}
	
	protected function _setData(){
		$grid = new Crud_Grid_Calendar(null, array('date'=>$this->_date));
		$grid->setLimit('all');
		$data = $grid->getData();
		$this->_data = $data;
	}
}