<?php
class ArOn_Crud_Form_ExtJs_GalleryTextarea extends ArOn_Crud_Form_ExtJs_Element {

	/**
	 * Enter description here...
	 *
	 * @var Zend_Form_File
	 */
	protected $_element;
	protected $_width = 615;

	public function init(){}

	protected function setup(){
		parent::setup();
	}

	public function render(){
		$html = " new Ext.form.HtmlEditor({
    		fieldLabel: '" . $this->_fieldLabel . "',
    		boxLabel: '" . $this->_boxLabel . "',
            name: '" . $this->_name . "',
            id: '".$this->_formActionName."-" . $this->_name . "-id-".$this->_element->getAttrib('actionId')."',
            allowBlank: '" . $this->_allowBlank . "',
            value: '" . addslashes($this->_value) . "',
            width: " . $this->_width . ",
            plugins: new Ext.ux.plugins.HtmlEditorImageInsert({
            	popNotShow: true,
            	dir: '/catalog/images/generations',
            	id: '".$this->_formActionName."-" . $this->_name . "-id-".$this->_element->getAttrib('actionId')."',
            })
		})";
		return $html;
	}
}