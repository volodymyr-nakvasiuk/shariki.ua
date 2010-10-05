<?php

class ArOn_Crud_Grid_Column_JoinMany extends ArOn_Crud_Grid_Column {

	protected $type = 'int';

	protected $_rules;
	protected $_tableField;
	protected $_separator = '<br>';
	protected $_orderBy;
	protected $_limit = false;

	function __construct($title, $rules = null, $tableField = null, $orderBy = null, $separator = null, $limit = false, $width = false, $render_function = false) {
		parent::__construct ( $title , true, false, $width, $render_function);
		$this->_rules = $rules;
		$this->_tableField = $tableField;
		$this->isSortField = false;
		$this->_orderBy = $orderBy;
		$this->_separator = $separator;
		if ($limit)
		$this->_limit = $limit;
	}

	public function updateCurrentSelect(ArOn_Db_TableSelect $select) {
		$this->loadHelper ();
		$select->columnsJoinMany ( $this->_rules, $this->name, $this->_tableField, $this->_orderBy );
		return $select;
	}

	public function render($row) {
		$value = $row [$this->name];
		$array = explode ( "\n", $value );
		$count = count ( $array );
		if(($this->_limit !== false) && ($this->_limit !== null))
			$array = array_slice ( $array, 0, $this->_limit );
		$value = implode ( $this->_separator, $array );
		if (($this->_limit !== false) && ($this->_limit !== null) && $count > $this->_limit)
			$value .= $this->_separator . "...";

		if (! empty ( $this->link )) {
			$value = $this->createActionLink ( $value, @$row [$this->gridTitleField] );
		}
		return $value;
	}

}