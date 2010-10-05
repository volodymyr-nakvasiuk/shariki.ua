<?php
class ArOn_Crud_Form_Field_Url extends ArOn_Crud_Form_Field {
	
	protected $_covertType = 'string';

	public function updateField() {
		parent::updateField ();
		// $validator = new Zend_Validate_Hostname()
		$this->element->addValidator ( 'Uri' );

	}

}