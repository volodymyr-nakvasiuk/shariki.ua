<?php
class ArOn_Crud_Form_ExtJs_File extends ArOn_Crud_Form_ExtJs_Element {

	/**
	 * Enter description here...
	 *
	 * @var Zend_Form_File
	 */
	protected $_element;
	protected $_inputType = 'file';

	public function init(){}

	protected function setup(){
		parent::setup();
	}

	public function render(){
		$html = " new Ext.ux.form.FileUploadField({
            emptyText: 'Выберите файл',
            buttonText: 'Обзор...',
    		fieldLabel: '" . $this->_fieldLabel . "',
    		boxLabel: '" . $this->_boxLabel . "',
            name: '" . $this->_name . "',
            //id: '" . $this->_id . "',
            allowBlank: '" . $this->_allowBlank . "',
            value: '" . $this->_value . "',
            width:" . $this->_width . "
		})";
		return $html;
	}
}