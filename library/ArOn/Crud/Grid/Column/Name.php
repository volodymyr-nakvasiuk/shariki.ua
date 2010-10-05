<?php
class ArOn_Crud_Grid_Column_Name extends ArOn_Crud_Grid_Column_Default {

	function __construct($title, $isSort = true) {
		parent::__construct ( $title, null, $isSort );
	}

	public function updateCurrentSelect(ArOn_Db_TableSelect $currentSelect) {
		$this->loadHelper ();
		$currentSelect->columnsName ( $this->key );
		return $currentSelect;
	}

}