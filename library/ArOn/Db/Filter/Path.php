<?php

class ArOn_Db_Filter_Path extends ArOn_Db_Filter {

	protected $_path;
	protected $_filter;

	public function __construct($path,ArOn_Db_Filter $filter) {
		$this->_path = $path;
		$this->_filter = $filter;
	}

	public function filterWhere(ArOn_Db_TableSelect $select, /*ArOn_Db_Table */ $table, $alias) {
		if (! $this->_path)
			return $this->_filter->filterWhere ( $select, $table, $alias );
		$joinPath = $table->getJoinPath ( $this->_path );
		$last = end ( $joinPath );
		$alias = $select->joinPath ( $joinPath );
		if ($joinPath) {
			$first = array_shift ( $joinPath );
			$selectIn = $first ['refTable']->select ()->columns ( $first ['refColumns'] );
			$aliasIn = $selectIn->joinPath ( $joinPath );
			if ($joinPath) {
				throw new Exception ( 'Could not filter embedded one-to-many rule: ' . reset ( array_keys ( $joinPath ) ) );
			}
			$this->_filter->filter ( $selectIn, $last ['refTable'], $aliasIn );
			return "`$alias`.`" . $first ['columns'] . "` IN ($selectIn)";
		}
		return $this->_filter->filterWhere ( $select, $last ['refTable'], $alias );
	}

}
