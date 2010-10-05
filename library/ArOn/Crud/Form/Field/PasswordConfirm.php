<?php
class ArOn_Crud_Form_Field_PasswordConfirm extends ArOn_Crud_Form_Field {

	protected $_type = 'password';
	protected $_covertType = 'string';
	protected $confirm_field;
	protected $required = 1;

	function __construct($name, $confirm_field, $title = null, $description = null, $required = null, $notEdit = false) {
		parent::__construct ( $name, $title, $description, $required, $notEdit );
		$this->confirm_field = $confirm_field;
	}

	public function updateField() {
		parent::updateField ();
		$validator = new ArOn_Zend_Validate_CompareToField ( );
		$validator->setField ( $this->confirm_field );
		$this->element->addValidator ( $validator );
		$this->element->setAllowEmpty ( false );

	}

	public function getInsertData() {
		return false;
	}
}
