<?php

class ArOn_Db_Filter_Id extends ArOn_Db_Filter_Field {

	public function __construct($expr) {
		parent::__construct ( null, $expr );
	}

	public function filterWhere(ArOn_Db_TableSelect $select, /*ArOn_Db_Table */ $table, $alias) {
		$this->_field = $table->getPrimary ();
		return parent::filterWhere ( $select, $table, $alias );
	}

}
