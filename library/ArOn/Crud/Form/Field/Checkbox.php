<?php
class ArOn_Crud_Form_Field_Checkbox extends ArOn_Crud_Form_Field {

	protected $_type = 'checkbox';
	
	/**
     * Value when checked
     * @var string
     */
	public $_value = 1;

    /**
     * Value when not checked
     * @var string
     */
    public $_uncheckedValue = '0';

	public $checked = false;

	public function createElement() {
		parent::createElement ();
		$this->element->setCheckedValue ( $this->_value );
		if ($this->checked)
		$this->element->setValue ( $this->_value );
		else
		$this->element->setValue ( false );
		return $this->element;
	}

	public function setValue($value) {
		if ($this->element instanceof Zend_Form_Element) {
			$this->element->setValue ( $value );
		}
		return $this;
		//$this->form->getElement($this->name)->setValue($value);
	}
	
	public function getValue() {
		if ($this->element instanceof Zend_Form_Element) {
			return (($this->element->isChecked())) ? $this->_value : $this->_uncheckedValue;
		}
		return $this->_value;

	}
	
}