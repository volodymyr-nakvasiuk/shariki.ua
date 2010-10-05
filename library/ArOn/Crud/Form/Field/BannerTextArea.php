<?php
class ArOn_Crud_Form_Field_BannerTextArea extends ArOn_Crud_Form_Field {

	protected $_type = 'textarea';

	public function updateField() {
		parent::updateField ();
		$this->element->addFilter ( 'StringTrim' )->addValidator ( new Zend_Validate_BannerDescription ( 2, 70 ) )->//->addValidator('Regex',false,array('/^[a-z][a-z0-9., \'-]{2,}$/i'))
		setAttribs ( array ('rows' => 4, 'cols' => 30 ) );
	}
}