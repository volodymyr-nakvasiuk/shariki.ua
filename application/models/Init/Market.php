<?php
class Init_Market {
	
	protected $_catData = false;
	protected $_tovData = array();
	
	public function __construct(){
	}
	
	public function getCatData(){
		if (!$this->_catdata) $this->_setCatData();
		return $this->_catdata;
	}

	public function getTovData($catId){
		if (!isset($this->_tovdata[$catId])) $this->_setTovData($catId);
		return $this->_tovdata[$catId];
	}
	
	protected function _setCatData(){
		$grid = new Crud_Grid_Marketc();
		$grid->setLimit('all');
		$data = $grid->getData();
		$this->_catdata = $data['data'];
	}

	protected function _setTovData($catId){
		$grid = new Crud_Grid_Marketd(null, array('catid'=>$catId));
		$grid->setLimit('all');
		$data = $grid->getData();
		$this->_tovdata[$catId] = $data['data'];
	}
}