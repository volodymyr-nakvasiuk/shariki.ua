<?php
class Init_Feedback {

	protected $_onPage = 7;
	protected $_data = false;
	protected $_p = false;
	
	public function __construct($p=1){
		$this->_p = $p;
	}
	
	public function getData(){
		if (!$this->_data) $this->_setData();
		return $this->_data;
	}
	
	protected function _setData(){
		$grid = new Crud_Grid_Feedback(null, array('p'=>$this->_p));
		$grid->setLimit($this->_onPage);
		$data = $grid->getData();
		$this->_data = $data;
	}
}