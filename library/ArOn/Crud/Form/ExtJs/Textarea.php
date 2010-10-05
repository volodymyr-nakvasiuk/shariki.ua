<?php
class ArOn_Crud_Form_ExtJs_Textarea extends ArOn_Crud_Form_ExtJs_Element {

	/**
	 * Enter description here...
	 *
	 * @var Zend_Form_File
	 */
	protected $_element;
	protected $_width = 615;
	protected $_plugins;

	public function init(){}

	protected function setup(){
		parent::setup();
		$_plugins = $this->_element->getAttrib('plugins');
		$this->_plugins = $_plugins?$_plugins:array();
	}

	public function render(){
		$html = " new Ext.form.HtmlEditor({
    		fieldLabel: '" . $this->_fieldLabel . "',
    		boxLabel: '" . $this->_boxLabel . "',
    		pluginsConfig:{" . implode(', ', $this->_plugins) . "},
            name: '" . $this->_name . "',
            //id: '" . $this->_id . "',
            allowBlank: '" . $this->_allowBlank . "',
            value: '" . addslashes($this->_value) . "',
            width: " . $this->_width . "
		})";
		return $html;
	}
}