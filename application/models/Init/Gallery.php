<?php
class Init_Gallery {

	protected $_data = false;
	protected $_parent_id = false;
	protected $_grid = false;

	public function __construct($parent_id){
		$this->_parent_id = $parent_id;
	}
	
	public function getData(){
		if (!$this->_data) $this->_setData();
		return $this->_data;
	}
	
	protected function _setData(){
		$this->_data = array();
		if ($this->_grid){
			$grid = new $this->_grid(null, array('parent_id'=>$this->_parent_id));
			$grid->setLimit('all');
			$data = $grid->getData();
			$this->_data = $data['data'];
		}
	}
}