<?php

class ArOn_Db_Filter_Match extends ArOn_Db_Filter {

	protected $_fields;
	protected $_expr;


	protected $_criteria;

	public function __construct($fields, $expr) {
		$this->_expr = $expr;
		$this->_fields = $fields;
	}

	protected function getFilterStr() {		
		return ArOn_Db_Table::getDefaultAdapter ()->quote ( $this->_expr );
	}

	public function filterWhere(ArOn_Db_TableSelect $select, /*ArOn_Db_Table */ $table, $alias) {
		$fields = array();
		foreach($this->_fields as $field){
			$fields[] = $table->applyAlias ( $field, $alias );
		}
		$expr = implode(',',$fields);
		return "MATCH (" . $expr . ") AGAINST (" . $this->getFilterStr () . ")";
	}

}
