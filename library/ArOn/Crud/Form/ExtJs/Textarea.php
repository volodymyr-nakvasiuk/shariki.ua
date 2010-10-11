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
/*
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
*/
	
	public function render(){
		$html = " new Ext.form.CKEditor({
			fieldLabel: '" . $this->_fieldLabel . "',
			boxLabel: '" . $this->_boxLabel . "',
			name: '" . $this->_name . "',
			//id: '" . $this->_id . "',
			allowBlank: '" . $this->_allowBlank . "',
			value: '" . addslashes($this->_value) . "',
			width: " . $this->_width . ",
			CKConfig:{
				toolbar: [
					['Source','Preview','Templates'],
					//['Save','NewPage'],
					['Cut','Copy','Paste','PasteText','PasteFromWord'],
					//['Print', 'SpellChecker', 'Scayt'],
					['Undo','Redo'],
					['Find','Replace'],
					['SelectAll','RemoveFormat'],
					//['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
					['Link','Unlink','Anchor'],
					'/',
					['Bold','Italic','Underline','Strike'],
					['Subscript','Superscript'],
					['NumberedList','BulletedList'],
					['Outdent','Indent','Blockquote','CreateDiv'],
					['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
					['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
					'/',
					['Styles','Format','Font','FontSize'],
					['TextColor','BGColor'],
					['Maximize', 'ShowBlocks'],
					['About']
				]
			}
		})";
		return $html;
	}
}