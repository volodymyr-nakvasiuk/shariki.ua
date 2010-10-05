<?php
class ArOn_Crud_Grid_Filter_Field_Array2Select extends ArOn_Crud_Grid_Filter_Field {

	protected $_type = 'select';

	public $options = array();

	public $field_all = true;

	protected $_criteria;
	const EQ = "EQ";
	const MORE = "MORE";
	const LESS = "LESS";

	function __construct($name, $title = null, $criteria = ArOn_Db_Filter_Search::EQ, $options = null) {
		$this->_criteria = $criteria;
		parent::__construct($name,$title,$options);
	}

	public function updateField() {
		parent::updateField ();
		$this->element->setRegisterInArrayValidator(false);
		if ($this->field_all)
		$this->element->addMultiOption ( '', '-' );
		$this->element->addMultiOptions ( $this->options );

	}

	public function applyFilter(ArOn_Db_TableSelect $select) {
		if($filters = $this->getFilters()) $select->filter ( $filters );
	}

	public function getFilters(){
		$values = $this->getFieldValue ();
		if ($values) {
			$filters = array();
			if(!is_array($values)) $values = array($values);
			foreach ($values as $val){
				if((empty($val)&&$val!==0&&$val!=='0') || in_array($val,$this->exceptionValues, true)) continue;
				$filters[] = new ArOn_Db_Filter_Field ($this->name ,$val );
			}
			if(!empty($filters) && $this->enableDefaultValue) {
				$val = $this->_defaultValue;
				$filters[] = new ArOn_Db_Filter_Field ($this->name ,$val);
			}
			$filter = new ArOn_Db_Filter_Compound('OR',$filters);
			return $filter;
		}
		return false;
	}

	public function getFieldWhere() {
		return null;
	}

	/*public function getFieldWhere() {
		$values = $this->getFieldValue ();
		if ($this->_criteria === self::EQ) {
		$delimeter = "=";
		} elseif ($this->_criteria === self::MORE) {
		$delimeter = ">=";
		} elseif ($this->_criteria === self::LESS) {
		$delimeter = "<=";
		}

		if (@ $values and $this->element->isValid ( $values )) {
		$where = array();
		if(!is_array($values)) $values = array($values);
		foreach ($values as $value){
		if(empty($value)) continue;
		$where[] = (empty ( $this->join_name )) ? "$this->table.$this->name $delimeter " . ArOn_Crud_Tools_String::quote ( $value ) : "$this->join_name $delimeter " . ArOn_Crud_Tools_String::quote ( $value );
		}
		return (!empty($where)) ? "(".implode(' OR ',$where).")": null;
		}else{
		//var_dump($this->element->getErrors());
		}
		}*/

}
