<?php
class ArOn_Crud_Form_Field_Array2Checkbox extends ArOn_Crud_Form_Field {

	protected $_type = 'multiCheckbox';

	protected $options = array ();

	public $onchange;

	public $separator = ' ';

	public function updateField() {

		parent::updateField ();

		$this->element->setMultiOptions ( $this->options )->setSeparator ( $this->separator )->setAttrib ( 'class', $this->_type );

		if (! empty ( $this->onchange ))
		$this->element->setAttrib ( 'onchange', $this->onchange );

	}

	public function setOptions($options) {
		$this->options = $options;
	}

	public function getInsertData() {
		if (! $this->saveInDataBase)
		return false;

		$value = $this->element->getValue ();

		if (is_array ( $value )) {
			$value = implode ( ',', $value );
		}
		$data = array ();
		$data ['model'] = 'default';
		$data ['data'] = array ('key' => $this->element->getName (), 'value' => $value );

		return $data;
	}

	public function setValue($value) {

		if (! is_array ( $value ) && is_string ( $value ))
		$value = explode ( ",", $value );

		return parent::setValue ( $value );
	}
}
