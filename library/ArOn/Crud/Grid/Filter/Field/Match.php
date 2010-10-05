<?php
class ArOn_Crud_Grid_Filter_Field_Match extends ArOn_Crud_Grid_Filter_Field {

	protected $_type = 'text';

	protected $_fields;

	function __construct($name, $title, $fields) {
		parent::__construct ( $name, $title );
		$this->_fields = is_array ( $fields ) ? $fields : array ($fields );		
	}

	public function applyFilter(ArOn_Db_TableSelect $select) {
		if($filters = $this->getFilters()) $select->filter ( $filters );
	}

	public function getFilters(){
		$values = $this->getFieldValue ();
		if ($values) {
			if(!is_array($values)){
				$values = $this->parseValue($values);
			}
			$filters = array();
			foreach ($values as $sub_values){
				$sub_filters = array();
				foreach ($sub_values as $value){
					if(empty($value)) continue;
					$value = trim($value);
					$sub_filters[]  = new ArOn_Db_Filter_Match ( $this->_fields,  $value );
				}
				if(!is_array($sub_filters)) continue;
				$filters[] = new ArOn_Db_Filter_Compound ( 'OR', $sub_filters );
			}
			if(!is_array($filters)) return false;
			$filter = new ArOn_Db_Filter_Compound ( 'OR', $filters );
			return $filter;
		}
		return false;
	}

	// backward compatibility
	public function getFieldWhere() {
		return null;
	}
	
	protected function parseValue($value){
		
		if(strpos($value,';') !== false){
			$values = array();
			$tmp_values = explode(';',$value);
			foreach ($tmp_values as $value){
				$values [] = $this->subParseValue($value);
			}
			return $values;			
		}
		else{
			return array($this->subParseValue($value));
		}
	}
	
	protected function subParseValue($value){
		/*$values = array();
		if(strpos($value,',') !== false){
			return explode(',',$value);			
		}
		else{
			return array($value);
		}*/
		
		return str_replace(","," ",$value);
	}
	
}
