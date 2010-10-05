<?php
class ArOn_Crud_Form_ExtJs_NotEdit extends ArOn_Crud_Form_ExtJs_Element {

	/**
	 * Enter description here...
	 *
	 * @var Zend_Form_File
	 */
	protected $_element;


	public function init(){}

	protected function setup(){
		parent::setup();
	}

	public function render(){
		$html = " new Ext.form.TextField({
    		fieldLabel: '" . $this->_fieldLabel . "',
    		boxLabel: '" . $this->_boxLabel . "',
            allowBlank: '" . $this->_allowBlank . "',
            value: '" . $this->_value . "',
            disabled: true,
            width: " . $this->_width . "
		}),
		 new Ext.form.Hidden({	
			name: '" . $this->_name . "',
			//id: '" . $this->_id . "',
            value: '" . $this->_value . "'
		})";

		return $html;
	}
}