<?php
class ArOn_Crud_Form_ExtJs_PasswordConfirm extends ArOn_Crud_Form_ExtJs_Element {
	
	protected $confirm_field_id;
	
	public function render(){
		$html = " new Ext.form.TextField( {
			inputType: 'password',
			vtype: 'password',
        	initialPassField: '" . $this->_confirm_field_id . "',
    		fieldLabel: '" . $this->_fieldLabel . "',
    		boxLabel: '" . $this->_boxLabel . "',
            name: '" . $this->_name . "',
            //id: '" . $this->_id . "',
            allowBlank: " . $this->_allowBlank . ",
            value: '" . addslashes($this->_value) . "',
            width:" . $this->_width . "
		})";
		return $html;
	}
	
}