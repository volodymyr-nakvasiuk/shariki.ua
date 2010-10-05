<?php
class ArOn_Crud_Grid_Column_FormColumn extends ArOn_Crud_Grid_Column {

	public $formClass;

	public $form;

	/**
	 * @var Zend_Form_Element
	 */
	protected $element;
	protected $decorator;

	protected $_element_view;
	protected $_element_helper;
	protected $_element_attribs;
	protected $_element_name;
	protected $_element_options;

	protected $attibutes;

	function __construct($title, $name = null, $isSort = true, $attibutes = array(), $width = false) {
		parent::__construct ( $title, $isSort, false, $width);
		$this->name = $name;
		$this->attibutes = $attibutes;
	}

	public function updateCurrentSelect($currentSelect) {
		$this->loadHelper ();
		$currentSelect->columns ( array ($this->key => $this->name ) );
		return $currentSelect;
	}

	public function init($grid, $grid_key) {
		parent::init ( $grid, $grid_key );
		if (! $this->form) {
			$this->form = ArOn_Crud_Tools_Registry::singleton ( $this->formClass ? $this->formClass : $grid->formClass );
		}
	}

	public function updateColumn() {
		parent::updateColumn ();

		$fieldName = $this->form->getFieldNameByColumnName ( $this->name );
		$this->form->fields [$fieldName]->setForm ( $this->form );
		$this->element = $this->form->fields [$fieldName]->createElement ();

		$this->_element_view = $this->element->getView ();
		$this->_element_helper = $this->element->helper;
		$this->_element_attribs = $this->element->getAttribs ();
		$this->_element_name = $this->element->getFullyQualifiedName ();
		$this->_element_options = $this->element->options;

		if ($this->element instanceof Zend_Form_Element_Checkbox) {
			$this->_element_options = array ('checked' => 1, 'unChecked' => 0 );
		}
	}
	
	public function setElementView($view) {
		$this->_element_view = $view;
	}

	public function render($row) {

		$attribs ['rowId'] = $this->row_id;
		$attribs ['id'] = $this->name . "-" . $this->row_id;

		foreach ( $this->attibutes as $key => $value ) {
			$attribs [$key] = $value;
		}

		if ($this->element instanceof Zend_Form_Element_Checkbox) {
			//			"<span id=\"0\"><input type=\"hidden\" name=\"is_active\" value=\"0\"><input type=\"checkbox\" name=\"is_active\" id=\"is_active\" value=\"1\" checked=\"checked\" rowId=\"0\"></span>"
			$attribs ['checked'] = $row [$this->key];
		}
		$fieldName = $this->form->getFieldNameByColumnName ( $this->name );
		$value = $this->form->fields [$fieldName]->setValue ( $row [$this->key] )->getValue ();
		$helper = $this->_element_helper;
		$html = ($this->_element_view instanceof Zend_View_Interface) ? "<span id=\"$this->row_id\">".$this->_element_view->$helper ( $this->_element_name, $value, $attribs, $this->_element_options )."</span>" : $value;

		return $html;
	}

	public function updateField($id,$value){
		if( $this->element->isValid($value) ){
			$model = $this->form->getModel();
			$fieldName = $this->form->getFieldNameByColumnName ( $this->name );
			$this->form->fields [$fieldName] -> setValue($value);
			$value = $this->form->fields [$fieldName] -> getValue();
			$column = $this->form->fields [$fieldName] -> getName();
			$data = array( $column => $value);
			$where = $model->q($model->getPrimary() . " = ?",$id);
			$result = $model->update($data,$where);
			return $result;
		}
		return array('valid' => false, 'errors' => $this->element->getErrors());
	}
}