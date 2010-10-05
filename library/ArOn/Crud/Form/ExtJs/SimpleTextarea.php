<?php
class ArOn_Crud_Form_ExtJs_SimpleTextarea extends ArOn_Crud_Form_ExtJs_Element {

	/**
	 * Enter description here...
	 *
	 * @var Zend_Form_File
	 */
	protected $_element;
	protected $_width = 615;
	protected $_height = 200;

	public function init(){}

	public function render(){
		$html = " new Ext.form.TextArea( {
			inputType: '" . $this->_inputType . "',
    		fieldLabel: '" . $this->_fieldLabel . "',
    		boxLabel: '" . $this->_boxLabel . "',
            name: '" . $this->_name . "',
            //id: '" . $this->_id . "',
            allowBlank: " . $this->_allowBlank . ",
            value: '" . addslashes($this->_value) . "',
            width:" . $this->_width . ",
            height:" . $this->_height . "
		})";
		return $html;
	}
}