<?php

abstract class ArOn_Db_Filter {

	abstract public function filterWhere(ArOn_Db_TableSelect $select, /*ArOn_Db_Table */ $table, $alias);

	public function filter(ArOn_Db_TableSelect $select, /*ArOn_Db_Table */ $table, $alias) {
		$where = $this->filterWhere ( $select, $table, $alias );
		if ($where) {
			$select->where ( $where );
		}
	}

}
