<?php
class ArOn_Crud_Form_ExtJs_Password extends ArOn_Crud_Form_ExtJs_Element {
	
	
	public function render(){
		$html = " new Ext.form.TextField( {
			inputType: 'password',
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
	/*new Ext.form.TextField({
            inputType: 'password',
            fieldLabel: 'Поле для пароля',
            name: 'pass',
            id: 'pass',
            allowBlank: false,
            value: '12345',
            width:190
        }),*/
}