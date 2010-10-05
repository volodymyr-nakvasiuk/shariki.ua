<?php
class ArOn_Crud_Tree{
	
	protected $_params;
	
	public $ajaxActionName;
	public static $ajaxModuleName;

	function __construct($params = array()) {
		$this->_params = $params;

		$this->init ();
		$this->setup ();
	}

	protected function init() {}

	public function setup() {}
	
	public function render() {return '';}
	
	public function renderNode() {
		$return = array();
		//$return[] = '{"text":"node text", "id":"node id", "leaf":false}';
		
		return '['.implode(', ', $return).']';
	}

	public function renderData() {return '{succes: true}';}
}
