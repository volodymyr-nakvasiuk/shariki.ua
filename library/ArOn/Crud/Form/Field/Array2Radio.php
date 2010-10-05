<?php
class ArOn_Crud_Form_Field_Array2Radio extends ArOn_Crud_Form_Field {

	protected $_type = 'radio';

	protected $_value = 0;

	protected $options;

	public $onchange;

	public $separator = ' ';

	public function updateField() {

		parent::updateField ();

		$this->element->setRegisterInArrayValidator ( false );
		$this->element->setMultiOptions ( $this->options )->setSeparator ( $this->separator )->setAttrib ( 'class', $this->_type );

		if (! empty ( $this->onchange ))
		$this->element->setAttrib ( 'onchange', $this->onchange );

	}

	public function setOptions($options) {
		$this->options = $options;
	}
	
	
	public function getValue() {
		$value = "";
		if ($this->element instanceof Zend_Form_Element) {
			$value = (($this->explode) ? explode($this->explode, $this->element->getValue ()) : $this->element->getValue ());
		}
		if($value !== "")
			return $value;
		if($this->_value === null)
			return $this->default;
		return (($this->explode) ? explode($this->explode, $this->_value) : $this->_value);

	}
}
