<?php
class ArOn_Crud_Form_Field_Text extends ArOn_Crud_Form_Field {

	public $height = 1000;
	protected $_covertType = 'string';

	public function updateField() {
		parent::updateField ();
		$this->element->addFilter ( 'StringTrim' );
		//->addValidator('Regex',false,array('/^[a-z][a-z0-9., \'-]{2,}$/i'))
		if ($this->height){
			$lengthValidator = new Zend_Validate_StringLength();
			$lengthValidator->setMax($this->height);
			$lengthValidator->setEncoding('utf-8');
			$this->element->addValidator ($lengthValidator);
		}
	}

}