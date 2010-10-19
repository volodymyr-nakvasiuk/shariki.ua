<?php
class ArOn_Crud_Window_ExtJs_Ckfinder extends ArOn_Crud_Window_ExtJs_Element{
	protected $_item_var = 'ckfinder';

	public function render(){
		$html = " new Ext.ux.CKFinder(".Zend_Json_Encoder::encode($this->_options).")";
		return $html;
	}
}