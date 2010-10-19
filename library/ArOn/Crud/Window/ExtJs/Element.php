<?php
class ArOn_Crud_Window_ExtJs_Element {
	protected $_options;
	protected $_name;
	protected $_id;
	protected $_width = 500;

	protected $_itemName = false;
	protected $_item_var = 'item';
	protected $_region = 'center';

	public function __construct($options){
		if ($options['config']){
			$this->_options = $options['config'];
			if ($options['width']) $this->_width = $options['width'];
			if ($options['name']) $this->_name = $options['name'];
			if ($options['id']) $this->_id = $options['id'];
			if ($options['item_var']) $this->_item_var = $options['item_var'];
			if ($options['region']) $this->_region = $options['region'];
		}
		else $this->_options = $options;

		$this->init();
		$this->setup();
	}

	public function init(){}

	protected function setup(){}

	public function render(){
		$html = " new Ext.BoxComponent(".Zend_Json_Encoder::encode($this->_options).")";
		return $html;
	}

	public function getItem(){
		if (!$this->_itemName) $this->setItem();
		return $this->_itemName;
	}

	protected function setItem(){
		$this->_itemName = str_replace('-', '__', $this->_item_var.'_'.$this->_region);
	}

}