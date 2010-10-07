<?php

class ArOn_Db_Filter_Field extends ArOn_Db_Filter {

	protected $_field;
	protected $_expr;

	const EQ = "EQ";
	const MORE = "MORE";
	const LESS = "LESS";

	protected $_criteria;

	public function __construct($field, $expr,$criteria = self::EQ) {
		$this->_expr = $expr;
		$this->_field = $field;
		$this->_criteria = $criteria;
	}

	protected function getFilterStr() {
		if ($this->_criteria === self::EQ) {
			$compare = " = ";
		} elseif ($this->_criteria === self::MORE) {
			$compare = " >= ";
		} elseif ($this->_criteria === self::LESS) {
			$compare = " <= ";
		} else {
			$compare = " = ";
		}
		if(!(strval(floatval($this->_expr))==$this->_expr) || (strpos($this->_expr,' ') !== false)) $this->_expr = ArOn_Db_Table::getDefaultAdapter ()->quote ( $this->_expr );
		return ($this->_expr instanceof Zend_Db_Expr) ? $this->_expr->__toString () : $compare . $this->_expr;
	}

	public function filterWhere(ArOn_Db_TableSelect $select, /*ArOn_Db_Table */ $table, $alias) {
		$expr = $table->applyAlias ( $this->_field, $alias );
		return $expr . " " . $this->getFilterStr ();
	}

}
