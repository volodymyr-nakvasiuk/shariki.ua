<?php
class ArOn_Crud_Form_Field_Numeric extends ArOn_Crud_Form_Field {

	protected $min;
	protected $max;
	protected $_covertType = 'float';
	
	public $default = 0;
	
	function __construct($name, $title = null, $description = null, $required = null, $notEdit = false, $min = 0, $max = 0x7fffffff) {

		$this->min = $min;
		$this->max = $max;

		parent::__construct ( $name, $title, $description, $required, $notEdit );
	}

	public function updateField() {
		parent::updateField ();
		$this->element->addValidator ( 'Float' );
		$this->element->addValidator ( 'Between', false, array ('messages' => array ('notBetween' => "'%value%' is not between '%min%' and '%max%'" ), 'Min' => $this->min, 'Max' => $this->max, 'Inclusive' => true ) );
	}
	
	public function getValue() {
		$value = "";
		if ($this->element instanceof Zend_Form_Element) {
			$value = (($this->explode) ? explode($this->explode, $this->element->getValue ()) : $this->element->getValue ());
		}
		if($value !== "")
			return $this->covertValueType($value);
		if($this->_value === null)
			return $this->covertValueType($this->default);
		return $this->covertValueType((($this->explode) ? explode($this->explode, $this->_value) : $this->_value));

	}
	
}