<?php
class ArOn_Crud_Form_Field_Currency extends ArOn_Crud_Form_Field {
	
	protected $_covertType = 'float';

	public function updateField() {
		parent::updateField ();

		$this->element->addValidator ( 'Float' );
		$validator = new Zend_Validate_GreaterThan ( '0.99999999999999' );
		$validator->setMessage ( "minimum value should be greater or equal 1" );
		$this->element->addValidator ( $validator );

	}

}