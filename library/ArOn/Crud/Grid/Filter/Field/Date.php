<?php
class ArOn_Crud_Grid_Filter_Field_Date extends ArOn_Crud_Grid_Filter_Field {

	public $compare = ">=";
	protected $dbFieldName;

	public function updateField() {
		parent::updateField ();
		$this->element->setRequired ( TRUE )->setValidators ( array (array ('NotEmpty', true ), new Zend_Validate_Date ( 'MM-DD-YYYY' ) ) );
		$this->element->helper = 'GridFormCalendar';
	}

	function __construct($name, $title = null, $options = null, $dbFieldName = null) {
		parent::__construct ( $name, $title, $options );
		$this->dbFieldName = $dbFieldName;
	}

	public function getFieldWhere() {

		$name = $this->name;
		if (! empty ( $this->dbFieldName )) {
			$name = $this->dbFieldName;
		}

		$value = $this->getFieldValue ();
		if (@ $value and $this->element->isValid ( $value )) {
			$value = str_replace ( "-", "/", $value );
			$value = date ( 'Y-m-d', strtotime ( $value ) );
			$where = (empty ( $this->join_name )) ? "$this->table.$name $this->compare " . ArOn_Crud_Tools_String::quote ( $value ) : "$this->join_name > " . ArOn_Crud_Tools_String::quote ( $value );
			return $where;
		}
	}
}
