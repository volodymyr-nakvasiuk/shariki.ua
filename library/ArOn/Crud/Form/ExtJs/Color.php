<?php
class ArOn_Crud_Form_ExtJs_Color extends ArOn_Crud_Form_ExtJs_Element {
	
	public function render(){
		$html = " new Ext.form.ColorField( {
    		fieldLabel: '" . $this->_fieldLabel . "',
            name: '" . $this->_name . "',
            //id: '" . $this->_id . "',
            value: '" . addslashes($this->_value) . "',
            width:" . $this->_width . "
		})";
		return $html;
	}
}