<?php
class ArOn_Crud_Grid_Column_Default extends ArOn_Crud_Grid_Column {

	function __construct($title, $name = null, $isSort = true, $hidden = false, $width = 80, $render_function = false, $info_block = false) {
		if ($info_block) {
			$this->link = true;
			$this->info_block = $info_block;
		}
		parent::__construct ( $title, $isSort, $hidden, $width, $render_function );
		$this->name = $name;
	}

	public function updateCurrentSelect(ArOn_Db_TableSelect $currentSelect) {
		$this->loadHelper ();
		$currentSelect->columns ( array ($this->key => $this->name ) );
		return $currentSelect;
	}

	public function render(array &$row) {
		$value = $row [$this->key];
		if ($this->link) {
			$value = $this->createActionLink ( $value, @$row [$this->gridTitleField] );
		}
		return $value;
	}
}