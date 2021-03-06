<?php
class Init_Services {
	
	protected $_data = false;
	
	public function __construct(){
	}
	
	public function getData(){
		if (!$this->_data) $this->_setData();
		return $this->_data;
	}
	
	protected function _setData(){
		$grid = new Crud_Grid_Services();
		$grid->setLimit('all');
		$data = $grid->getData();
		$this->_data = $data['data'];
	}
}