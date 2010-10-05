<?php
class ArOn_Crud_Form_ExtJs_Checkbox extends ArOn_Crud_Form_ExtJs_Element {

	/**
	 * Enter description here...
	 *
	 * @var Zend_Form_Checkbox
	 */
	protected $_element;

	protected $_checked = 'false';

	public function init(){}

	protected function setup(){
		parent::setup();
		if($this->_element->getValue() == true) $this->_checked = 'true';
		$this->_value = $this->_element->getCheckedValue();

	}

	public function render(){
		$html = " new Ext.form.Checkbox( {
    		fieldLabel: '" . $this->_fieldLabel . "',
    		boxLabel: '" . $this->_boxLabel . "',
            name: '" . $this->_name . "',
            //id: '" . $this->_id . "',
            allowBlank: " . $this->_allowBlank . ",
            inputValue: '" . $this->_value . "',
            width:" . $this->_width . ",            
            checked: " . $this->_checked . "
		})";
		return $html;
	}

}