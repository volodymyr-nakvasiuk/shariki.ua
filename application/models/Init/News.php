<?php
class Init_News {
	
	protected $_data = false;
	protected $_limit = 20;
	
	public function __construct($limit=20){
		$this->_limit = $limit;
	}
	
	public function getData(){
		if (!$this->_data) $this->_setData();
		return $this->_data;
	}
	
	protected function _setData(){
		$grid = new Crud_Grid_News();
		$grid->setLimit($this->_limit);
		$data = $grid->getData();
		$this->_data = $data;
	}
}