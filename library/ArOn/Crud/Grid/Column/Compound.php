<?php
class ArOn_Crud_Grid_Column_Compound extends ArOn_Crud_Grid_Column {

	protected $_columns;
	protected $_separator;

	function __construct(array $columns, $separator = "<br>", $title = null) {
		$this->_separator = $separator;
		$this->_columns = $columns;
		parent::__construct ( $title );
	}

	public function getTitle() {
		if ($this->title === null) {
			$titles = array ();
			foreach ( $this->_columns as $column ) {
				$column_title = $column->getTitle ();
				if ($column_title != null) {
					$titles [] = $column_title;
				}
			}
			$this->title = implode ( $this->_separator, $titles );
		}
		return $this->title;
	}

	public function init($grid, $key) {
		parent::init ( $grid, $key );
		foreach ( $this->_columns as $k => $column ) {
			$strs [] = $column->init ( $grid, $k );
		}
	}

	public function render($row) {
		$strs = array ();
		foreach ( $this->_columns as $column ) {
			$column->row_id = $this->row_id;
			$name = $column->getName ();
			$strs [] = '<span class="row-' . $name . '">' . $column->render ( $row ) . "</span>";
		}
		return implode ( $this->_separator, $strs );
	}

	public function updateCurrentSelect($currentSelect) {
		foreach ( $this->_columns as $column ) {
			$currentSelect = $column->updateCurrentSelect ( $currentSelect );
		}
		return $currentSelect;
	}

	public function isSorted() {
		return reset ( $this->_columns )->isSorted ();
	}

	public function setRowId($value) {
		$this->row_id = $value;
		foreach ( $this->_columns as $column ) {
			$column->setRowId ( $value );
		}
	}

	public function getColumns() {
		return $this->_columns;
	}

	public function getColumn($key) {
		return $this->_columns [$key];
	}

}
