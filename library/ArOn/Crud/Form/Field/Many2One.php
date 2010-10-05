<?php
class ArOn_Crud_Form_Field_Many2One extends ArOn_Crud_Form_Field {

	protected $_type = 'select';

	public $model;

	protected $key;

	public $id;

	public $nullElement = false;

	public $onchange;

	public $optionName;
	public $category;
	public $categoryName;
	public $emptyCategory;
	public $emptyItem;
	public $where;

	public $field_edit = false;

	protected $required = true;

	public function updateField() {
		parent::updateField ();
		if (is_string ( $this->model ))
		$this->model = ArOn_Crud_Tools_Registry::singleton ( $this->model );
		if (! is_object ( $this->model ))
		return;
		//$data = (!empty($this->id))?$this->model->getListStandart($this->model->e($this->key,$this->id)):$this->model->getListStandart();
		$select = $this->model->select ();
		$options = ArOn_Crud_Tools_Multiselect::prepareOptions ( $select, $this->optionName, $this->category, $this->categoryName, $this->where, $this->emptyCategory, $this->emptyItem );

		if ($this->field_edit) {
			$action = '/' . ArOn_Crud_Form::$ajaxModuleName . '/' . $this->field_edit . '/edit/';
			$this->element->setAttrib ( 'edit', $action );
			$this->element->helper = 'MyFormSelectEdit';
		}

		if ($this->hidden or $this->notEdit or $this->category) {
			$this->element->setRegisterInArrayValidator ( false );
		}
		//echo $this->id."- $this->name<br>";
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
		if (! empty ( $this->onchange ))
		$this->element->setAttrib ( 'onchange', $this->onchange );
		$this->element->addMultiOptions ( $options );

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
	
	public function getRenderValue(){
		$value = $this->getValue();
		$option = $this->element->getMultiOption ($value);
		return $option;
	}
	
}
