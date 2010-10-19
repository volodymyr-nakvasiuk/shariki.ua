<?php

class ArOn_Db_TableSelect extends Zend_Db_Table_Select {

	/*
	 * @var ArOn_Db_Table
	 * */
	protected $_table;
	protected $_primary;
	protected $_tableName;
	protected $_is_deleted;
	protected $_is_deleted_value;
	const SEPARATOR = "\n";
	const ALIAS = "t";
	protected $_alias = self::ALIAS;

	public function __construct($table, $alias = null) {
		//		if ($alias) $this->_alias = $alias;
		$this->_alias = $alias ? $alias : $table->getTableName ();
		//		if ($alias) $this->_alias = $alias;
		parent::__construct ( $table );
		$this->setIntegrityCheck ( false );
	}

	public function setTable(Zend_Db_Table_Abstract $table) {
		$info = parent::setTable ( $table );
		$this->_table = $table;
		$this->_primary = $this->_info [Zend_Db_Table::PRIMARY] [1];
		$this->_tableName = $table->getTableName ();
		$this->_is_deleted = $table->getIsDeleted ();
		$this->_is_deleted_value = $table->getIsDeletedValue ();
		$this->columnsReset ();
		$this->filterDeleted ( false );
		return $info;
	}

	public function setColumn($name, $alias = null) {
		if (null !== $alias) {
			$this->_tableCols ( $this->_alias, array ($name => $alias ) );
		} else {
			$this->_tableCols ( $this->_alias, $name );
		}
		return $this;
	}

	public function setGroupConcatSeparator($separator) {
		$this->_group_concat_separator = $separator;
	}

	public function getTable() {
		return $this->_table;
	}

	public function getTableName() {
		return $this->_tableName;
	}

	public function columnsReset() {
		$this->reset ( self::COLUMNS );
		$this->reset ( self::FROM );
		$this->from ( array ($this->_alias => $this->_tableName ), array () );
		return $this;
	}

	public function columnsAll() {
		$this->_tableCols ( $this->_alias, self::SQL_WILDCARD );
		return $this;
	}

	public function columnsId($idCol = "id") {
		$this->_tableCols ( $this->_alias, array ($idCol => $this->_primary ) );
		return $this;
	}

	public function columnsName($nameCol = "name") {
		$this->_tableCols ( $this->_alias, array ($nameCol => $this->_table->getNameExpr ( $this->_alias ) ) );
		return $this;
	}

	public function columnsJoinManyEx($joinPath, $alias = null, $tableField = null, $orderBy = null, $separator = null) {
		if (! is_array ( $path ))
		$path = array ($path );
		$joinPath = $this->_table->getJoinPath ( $path );
	}

	const COUNT = "COUNT(*)";

	public function columnsJoinMany($path, $fieldAlias = null, $tableField = null, $orderBy = null, $separator = null) {
		if (! $path) {
			throw new Exception ( "Non empty path is required, table '" . get_class ( $this->_table ) . "'" );
		}
		if (! is_array ( $path ))
		$path = array ($path );
		$joinPath = $this->_table->getJoinPath ( $path );
		$joinPath = array_reverse ( $joinPath );
		$prevRef = array_shift ( $joinPath );
		$table = $prevRef ["refTable"];

		$joinPathRev = array ();
		foreach ( $joinPath as $rule => &$ref ) {
			if (! isset ( $ref ['reverse'] )) {
				throw new Exception ( "Only many-to-one references are supported, rule '$rule', table '" . $this->_table->getClass() . "'" );
			}
			$newRef ['columns'] = $prevRef ['refColumns'];
			$newRef ['refColumns'] = $prevRef ['columns'];
			$newRef ['refTable'] = $ref ['refTable'];
			$prevRef = $ref;
			$joinPathRev [$rule] = $newRef;
		}

		$select = $table->select ( "m" );
		$field = ($tableField !== null) ? $tableField : $select->getTable ()->getNameExpr ( "m" );
		if (! ($field instanceof Zend_Db_Expr))
		$field = "`m`.`$field`";
		if ($separator === null)
		$separator = self::SEPARATOR;
		$separator = $this->_table->getAdapter ()->quote ( $separator );
		if ($tableField === self::COUNT) {
			$select->columns ( array ("c" => new Zend_Db_Expr ( "COUNT(*)" ) ) );
		} else {
			if ($orderBy) {
				$select->columns ( array ("c" => new Zend_Db_Expr ( "LEFT(GROUP_CONCAT($field ORDER BY $orderBy SEPARATOR $separator), 250)" ) ) );
			} else {
				$select->columns ( array ("c" => new Zend_Db_Expr ( "LEFT(GROUP_CONCAT($field SEPARATOR $separator), 250)" ) ) );
			}
		}
		$alias = $select->joinPath ( $joinPathRev );

		$select->where ( "`$alias`." . "`" . $prevRef ['refColumns'] . "` = `" . $this->_alias . "`.`" . $this->_primary . "`" );

		if ($fieldAlias == null)
		$fieldAlias = $table->getClass();
		$this->columns ( array ($fieldAlias => new Zend_Db_Expr ( "($select)" ) ) );

		return $this;
	}

	public function columnsJoinOne($path, $columns) {
		$joinPath = $this->_table->getJoinPath ( $path );

		$current_table = $this->_alias;
		if(is_array($joinPath))
		foreach ( $joinPath as $rule => &$ref ) {
			if (isset ( $ref ['reverse'] ))
			return;
			$refTableName = $ref ['refTable']->getTableName ();
			if (! array_key_exists ( $rule, $this->_parts [self::FROM] )) {
				$this->joinLeft ( array ($rule => $refTableName ), "`$current_table`.`" . $ref ['columns'] . "` = " . "`$rule`.`" . $ref ['refColumns'] . "`", null );
			}
			$current_table = $rule;
		}
		if(empty($columns))
			return $this;
		if (is_string ( $columns ))
		$columns = array ($columns => null );
		if(!empty($columns))
			foreach ( $columns as $alias => &$tableField ) {
				if (! $tableField || $tableField === "NAME")
				$tableField = $ref ['refTable']->getNameExpr ( $rule );
				elseif ($tableField === "ID")
				$tableField = $ref ['refTable']->getPrimary ();
				else
				$tableField = $ref ['refTable']->applyAlias ( $tableField, $rule );
			}

		$this->columns ( $columns, $rule );

		return $this;
	}

	public function getRowCount() {
		$select = clone $this;
		$select->reset ( self::COLUMNS );
		$select->reset ( self::LIMIT_COUNT );
		$select->reset ( self::LIMIT_OFFSET );
		$select->reset ( self::ORDER );
		$select_catch = clone $select;
		if(ArOn_Db_Table::isCached()){
			$table = get_class($this->_table);
			$this->_table = ArOn_Db_Table::getInstance($table);
		}
		try {
			$select->reset ( self::FROM );
			$select->from($this->_table->getTableName(),array('count' => 'count(*)'));
			$result = $this->_table->fetchRow ( $select );
			$count = ($result === null) ? 0 : $result->count;
		}catch (Exception $e){
			$select_catch->from(null,array('count' => 'count(*)'));
			$result = $this->_table->fetchRow ( $select_catch );
			$count = ($result === null) ? 0 : (int) $result->count;
		}
		return $count;
	}

	public function orderNatural($reverse = false) {
		$direction = $this->_table->getOrderAsc ();
		if ($order = $this->_table->getOrderExpr ()){
			if(!is_array($order)) $order = array($order);
			if(!is_array($direction)) $direction = array($direction);
			$this->orderReset();
			foreach($order as $i => $key){
				$direction[$i] = ($direction[$i] ^ $reverse) ? "ASC" : "DESC";
				$this->_parts [self::ORDER] [] = array($key, $direction[$i]);
			}
		}
		return $this;
	}

	public function orderReset() {
		$this->_parts [self::ORDER] = array ();
		return $this;
	}

	public function joinPath(&$path) {
		if (! $path)
		return $this->_alias;
		$alias = $this->_alias;
		foreach ( $path as $rule => &$ref ) {
			if (isset ( $ref ['reverse'] ))
			return $alias;
			$refTableName = $ref ['refTable']->getTableName ();
			if (! array_key_exists ( $rule, $this->_parts [self::FROM] )) {
				$this->joinLeft ( array ($rule => $refTableName ), "`$alias`.`" . $ref ['columns'] . "` = " . "`$rule`.`" . $ref ['refColumns'] . "`", null );
			}
			$alias = $rule;
			unset ( $path [$rule] );
		}
		return $alias;
	}

	public function filterId($expr) {
		$alias = $this->_alias;
		if ($expr instanceof Zend_Db_Expr) {
			$this->where ( "`$alias`.`$this->_primary` " . $expr );
		} else {
			$this->where ( "`$alias`.`$this->_primary` = " . $this->_table->getAdapter ()->quote ( $expr ) );
		}
		return $this;
	}

	public function filter(ArOn_Db_Filter $filter) {
		$alias = $this->_alias;
		$filter->filter ( $this, $this->_table, $alias );
		return $this;
	}

	public function filterPath($path, ArOn_Db_Filter $filter) {
		$filterPath = new ArOn_Db_Filter_Path ( $path, $filter );
		$this->filter ( $filterPath );
		return $this;
	}

	public function filterDeleted($showDeleted) {
		if (! $this->_is_deleted)
		return $this;

		$whereShow = "`$this->_alias`.`$this->_is_deleted` = ".$this->_is_deleted_value;
		$whereHide = "`$this->_alias`.`$this->_is_deleted` = ".($this->_is_deleted_value) ? 1 : 0;
		$pos = array_search ( $whereShow, $this->_parts [self::WHERE] );
		if ($pos === null)
		$pos = array_search ( $whereHide, $this->_parts [self::WHERE] );
		if ($pos !== null)
		unset ( $this->_parts [self::WHERE] [$pos] );

		if ($showDeleted === null)
		return $this;
		if ($showDeleted) {
			$this->_parts [self::WHERE] [] = $whereShow;
		} else {
			$this->_parts [self::WHERE] [] = $whereHide;
		}
		return $this;
	}

	public function getAlias() {
		return $this->_alias;
	}

	public function removeDuplicateColumns() {
		$columns = array ();
		foreach ( $this->_parts [self::COLUMNS] as $id => $col ) {
			if(null === $col [2] && $col[1] instanceof Zend_Db_Expr){
				$columns [] = $col;
			}else{
				$columns [$col [2] ? $col [2] : $col [1]] = $col;
			}
			
		}
		$this->_parts [self::COLUMNS] = array_values ( $columns );
	}

	public function assemble() {
		$fields = $this->getPart ( Zend_Db_Table_Select::COLUMNS );
		// If no fields are specified we assume all fields from primary table
		if (! count ( $fields )) {
			$this->_tableCols ( $this->_alias, self::SQL_WILDCARD );
			$fields = $this->getPart ( Zend_Db_Table_Select::COLUMNS );
		}
		return Zend_Db_Select::assemble ();
	}

}
