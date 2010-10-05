<?php
class ArOn_Crud_Form_Field_Array2Select extends ArOn_Crud_Form_Field {

	protected $_type = 'select';

	protected $options;
	public $nullElement = false;
	public $onchange;
	public $action_id = null;

	public function updateField() {
		parent::updateField ();

		//			print_r($this->element->getDecorators()); die;
		//			unset($this->element->options);


		if (! empty ( $this->action_id )) {
			$this->element->helper = 'MyFormSelectText';
			$this->element->setAttrib ( 'action_id', $this->action_id );
			$this->element->setRegisterInArrayValidator ( false );
		}
		if ($this->nullElement or ! $this->required) {
			if (is_string ( $this->nullElement )) {
				$this->element->addMultiOption ( '', $this->nullElement );
			} elseif (is_array ( $this->nullElement )) {
				reset ( $this->nullElement );
				list ( $key1, $val1 ) = each ( $this->nullElement );
				$this->element->addMultiOption ( $key1, $val1 );
			} else {
				//$this->element->addMultiOption ( '', 'none' );
			}
		}
		$this->element->setRegisterInArrayValidator ( false );
		if (! empty ( $this->onchange ))
			$this->element->setAttrib ( 'onchange', $this->onchange );
		$this->element->addMultiOptions ( $this->options );
	}
	
	public function getValue() {
		$value = "";
		if ($this->element instanceof Zend_Form_Element) {
			$value = (($this->explode) ? explode($this->explode, $this->element->getValue ()) : $this->element->getValue ());
		}
		if($value === "")
			return (($this->explode) ? explode($this->explode, $this->_value) : $this->_value);
		else return $value;

	}
	
	public function setOptions($options) {
		$this->options = $options;
		return $this;
	}
	
	public function getRenderValue(){
		$value = $this->getValue();
		$option = $this->element->getMultiOption ($value);
		return $option;
	}
	
}
