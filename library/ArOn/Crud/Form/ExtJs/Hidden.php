<?php
class ArOn_Crud_Form_ExtJs_Hidden extends ArOn_Crud_Form_ExtJs_Element {

	/**
	 * Enter description here...
	 *
	 * @var Zend_Form_File
	 */
	protected $_element;
	protected $_inputType = 'hidden';

	public function init(){}

	protected function setup(){
		parent::setup();
	}

	public function render(){
		$html = " new Ext.form.Hidden({
			name: '" . $this->_name . "',
			//id: '" . $this->_id . "',
			value: '" . $this->_value . "'
		})";
		return $html;
	}
}