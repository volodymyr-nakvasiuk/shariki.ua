<?php
class ArOn_Crud_Grid_Filter_Field_Checkbox extends ArOn_Crud_Grid_Filter_Field {

	protected $_type = 'checkbox';
	protected $value = 1;

	public function getFieldWhere1() {

		$true = $this->element->isChecked();
		$value = $this->value;
		if ($this->_criteria === self::EQ) {
			$delimeter = "=";
		} elseif ($this->_criteria === self::MORE) {
			$delimeter = ">=";
		} elseif ($this->_criteria === self::LESS) {
			$delimeter = "<=";
		}

		//if (@ $value and $this->element->isValid ( $value )) {
		if($true){
			$where = (empty ( $this->join_name )) ? "$this->table.$this->name $delimeter " . ArOn_Crud_Tools_String::quote ( $value ) : "$this->join_name $delimeter " . ArOn_Crud_Tools_String::quote ( $value );
			return $where;
		}
	}

	public function getFilters(){
		$true = $this->element->isChecked();
		$values = $this->value;
		if ($true) {
			$filters = array();
			if(!is_array($values)) $values = array($values);
			foreach ($values as $val){
				$filters[] = new ArOn_Db_Filter_Field ($this->name ,$val , $this->criteria);
			}
			if(!empty($filters) && $this->enableDefaultValue) {
				$val = $this->_defaultValue;
				$filters[] = new ArOn_Db_Filter_Field ($this->name ,$val , $this->criteria);
			}
			$filter = new ArOn_Db_Filter_Compound('OR',$filters);
			return $filter;
		}
		return false;
	}

	public function getFieldWhere() {
		return null;
	}

	public function setValue($value){
		$this->value = $value;
		return $this;
	}
}