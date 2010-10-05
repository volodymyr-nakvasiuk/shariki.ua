<?php
class ArOn_Crud_Form_Field_Phone extends ArOn_Crud_Form_Field {
	public $errorMessage = 'Invalid phone number';

	public function updateField() {
		parent::updateField ();
		$validator = new Zend_Validate_Regex ( '/^[ +()_-\d]{6,}(\s(x|ext\.?)\s?\d{3,4})?$/i' ); //((\(\d{3}\) ?)|(\d{3}[- \.]))?\d{3}[- \.]\d{4}(\s(x\d+)?){0,1}$
		$validator->setMessage ( $this->errorMessage );
		$this->element->setRequired ( $this->required )->setValidators ( array (array ('NotEmpty', true ), $validator ) )->addFilter ( 'StringTrim' );
	}
}
