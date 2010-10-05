<?php
class ArOn_Crud_Form_Field_ReCheckbox extends ArOn_Crud_Form_Field {

	protected $_type = 'checkbox';

	public $_value = '1';

	public $checked = false;

	public function createElement() {
		$description = $this->description;
		$this->description = NULL;

		parent::createElement ();
		$this->element->setCheckedValue ( $this->_value );
		if ($this->checked != false)
		$this->element->setValue ( true );
		$this->element->setAttrib ( 'description', $description );
		$this->element->helper = 'MyFormReCheckbox';

		return $this->element;
	}
}