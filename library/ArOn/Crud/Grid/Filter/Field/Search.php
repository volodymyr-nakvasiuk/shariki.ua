<?php
class ArOn_Crud_Grid_Filter_Field_Search extends ArOn_Crud_Grid_Filter_Field {

	protected $_type = 'text';

	protected $_fields;

	function __construct($name, $title, $fields) {
		parent::__construct ( $name, $title );
		$this->_fields = is_array ( $fields ) ? $fields : array ($fields =>ArOn_Db_Filter_Search::LIKE );
		if (! is_array ( reset ( $this->_fields ) )) {
			$this->_fields = array (array ('path' => null, 'filters' => $this->_fields ) );
		}
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
					$tmp_filters = array();
					foreach ( $this->_fields as $filterSetting ) {
						if(array_key_exists('path',$filterSetting))
							$tmp_filters [] = new ArOn_Db_Filter_Path ( $filterSetting ['path'], new ArOn_Db_Filter_Search ( $filterSetting ['filters'], $value ) );
						elseif(array_key_exists('cache',$filterSetting)){
							$cfilter = new ArOn_Db_Filter_Cache ($value, $filterSetting ['field'], $filterSetting ['cache'] ,$filterSetting ['criteria']);							
							if(true === $cfilter->isCached()){
								$tmp_filters [] = $cfilter;
								break; 
							}
						}
					}
					if(!is_array($tmp_filters)) continue;
					$sub_filters [] = new ArOn_Db_Filter_Compound ( 'OR', $tmp_filters );
				}
				if(!is_array($sub_filters)) continue;
				$filters[] = new ArOn_Db_Filter_Compound ( 'AND', $sub_filters );
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
		$values = array();
		if(strpos($value,',') !== false){
			return explode(',',$value);			
		}
		else{
			return array($value);
		}
	}
	
}
