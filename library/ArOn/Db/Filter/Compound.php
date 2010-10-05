<?php

class ArOn_Db_Filter_Compound extends ArOn_Db_Filter {

	protected $_filters;
	protected $_glue;

	public function __construct($glue = 'OR', array $filters) {
		$this->_glue = $glue;
		$this->_filters = $filters;
	}

	public function filterWhere(ArOn_Db_TableSelect $select,  /*ArOn_Db_Table */ $table, $alias) {
		$wheres = array ();
		foreach ( $this->_filters as $filter ) {
			$where = ($filter instanceof ArOn_Db_Filter) ? $filter->filterWhere ( $select, $table, $alias ): $filter;
			if ($where)
			$wheres [] = $where;
		}
		if (! $wheres)
		return null;
		if (count ( $wheres ) == 1)
		return $wheres [0];
		$where = "(" . implode ( " $this->_glue ", $wheres ) . ")";
		return $where;
	}

}