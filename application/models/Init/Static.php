<?php
class Init_Static {
	
	protected $_data = false;
	protected $_actionId = 0;
	
	public function __construct($actionId = 0){
		$this->_actionId = $actionId;
	}
	
	public function getData(){
		if (!$this->_data) $this->_setData();
		return $this->_data;
	}
	
	protected function _setData(){
		if ($this->_actionId){
			$grid = new Crud_Grid_Static(null, array('staticaction'=>$this->_actionId));
			$data = $grid->getData();
			$this->_data = isset($data['data'][0])?$data['data'][0]:array();
		}
		else {
			$this->_data = array();
		}
	}
}