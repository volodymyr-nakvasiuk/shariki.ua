<?php

class ArOn_Crud_Grid_Column_JoinOne extends ArOn_Crud_Grid_Column {

	protected $type = 'int';

	protected $_rules;
	protected $_tableField;
	protected $_tableFields;
	protected $_na = "---";

	function __construct($title, $rules = null, $tableField = null, $tableFields = null, $hidden = false, $width = 120) {
		parent::__construct ( $title , true, $hidden, $width);
		$this->_rules = $rules;
		$this->_tableField = $tableField;
		$this->_tableFields = $tableFields;
	}

	public function updateCurrentSelect(ArOn_Db_TableSelect $select) {
		$tablefileds =  array ($this->key => $this->_tableField, $this->key . "_id" => "ID" );
		if($this->_tableFields) $tablefileds = (is_array($this->_tableFields)) ? array_merge($tablefileds,$this->_tableFields) : array_merge($tablefileds,array($this->_tableFields=>$this->_tableFields));
		$select->columnsJoinOne ( $this->_rules, $tablefileds );
		return $select;
	}

	public function render($row) {
		$value = $row [$this->key];
		if ($value && $this->link) {
			$this->row_id = $row [$this->key . "_id"];
			$value = $this->createActionLink ( $value, @$row [$this->gridTitleField] );
		}
		return $value ? $value : $this->_na;
	}
	
	public function setEmptyValue($na = '---'){
		$this->_na = $na;
		return $this;
	}
}