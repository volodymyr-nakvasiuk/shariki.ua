<?php
class ArOn_Crud_Form_Field_Mail extends ArOn_Crud_Form_Field {

	public function updateField() {
		parent::updateField ();
		$validator = new Zend_Validate_Regex ( '/^([a-zA-Z0-9_\.\-+])+@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/i' );
		$validator->setMessage ( 'Invalid email address' );
		$this->element->setRequired ( TRUE )->setValidators ( array (array ('NotEmpty', true ), //'EmailAddress'
		$validator ) )->addFilter ( 'StringTrim' );//->addDecorator('Label', array('tag' => 'th','requiredSuffix' => ' (*)'))

	}
}