<?php

class ArOn_Db_Filter_Search extends ArOn_Db_Filter {

	const ID = ArOn_Db_Table::ID;
	const NAME = ArOn_Db_Table::NAME;

	const EQ = "EQ";
	const LIKE = "LIKE";
	const BEGINS = "BEGIN";
	const MORE = "MORE";
	const LESS = "LESS";

	protected $_fields;
	protected $_expr;

	public function __construct($fields, $expr) {
		$this->_fields = is_array ( $fields ) ? $fields : array ($fields => self::EQ );
		$this->_expr = $expr;
	}

	public function filterWhere(ArOn_Db_TableSelect $select, /*ArOn_Db_Table */ $table, $alias) {
		$where = array ();
		$exprEq = ArOn_Db_Table::getDefaultAdapter ()->quote ( $this->_expr );
		$exprLike = ArOn_Db_Table::getDefaultAdapter ()->quote ( "%$this->_expr%" );
		$exprBegins = ArOn_Db_Table::getDefaultAdapter ()->quote ( "$this->_expr%" );
		foreach ( $this->_fields as $field => $criteria ) {
			if ($field === self::ID) {
				$field = $table->getPrimary ();
			} elseif ($field === self::NAME) {
				$field = $table->getNameExpr ();
			}
			$field = $table->applyAlias ( $field, $alias );
				
			if ($criteria === self::EQ) {
				$where [] = "$field = $exprEq";
			} elseif ($criteria === self::LIKE) {
				$where [] = "$field LIKE $exprLike";
			} elseif ($criteria === self::BEGINS) {
				$where [] = "$field LIKE $exprBegins";
			} elseif ($criteria === self::MORE) {
				$where [] = "$field > $exprEq";
			} elseif ($criteria === self::LESS) {
				$where [] = "$field < $exprEq";
			}
		}
		if ($where) {
			$where = implode ( " OR ", $where );
			return "($where)";
		}
	}

}