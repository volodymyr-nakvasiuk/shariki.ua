<?php
class ArOn_Crud_Window{
	
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
}
