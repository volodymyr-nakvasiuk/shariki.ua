<?php
class ArOn_Crud_Form_Field_Submit extends ArOn_Crud_Form_Field {
	protected $_type = 'submit';

	public function createElement() {
		parent::createElement ();
		$this->element->setAttrib ( 'class', 'submit' );
		$this->element->addDecorator ( 'htmlTag', array ('tag' => 'td' ) );

		return $this->element;
	}
}