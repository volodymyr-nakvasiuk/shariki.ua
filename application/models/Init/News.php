<?php
class Init_News {
	
	protected $_data = false;
	protected $_limit = 20;
	protected $_where = false;
	protected $_sort = 'DESC';
	
	public function __construct($limit=20, $where=false, $sort='DESC'){
		$this->_limit = $limit;
		$this->_where = $where;
		$this->_sort = $sort;
	}
	
	public function getData(){
		if (!$this->_data) $this->_setData();
		return $this->_data;
	}
	
	protected function _setData(){
		$grid = new Crud_Grid_News();
		$grid->setLimit($this->_limit);
		$grid->direction = $this->_sort;
		if ($this->_where) $grid->where = $this->_where;
		$data = $grid->getData();
		$this->_data = $data;
	}
}