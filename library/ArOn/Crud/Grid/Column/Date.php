<?php
class ArOn_Crud_Grid_Column_Date extends ArOn_Crud_Grid_Column_Default {

	protected $type = 'date';

	public $dateFormat = '%m/%e/%Y';

	public function updateCurrentSelect(ArOn_Db_TableSelect $currentSelect) {
		$this->loadHelper ();
		$alias = $currentSelect->getAlias ();
		$currentSelect->columns ( array ($this->key . "_str" => "DATE_FORMAT(`$alias`.`$this->name`, '" . $this->dateFormat . "')" ) );
		$currentSelect->columns ( array ($this->key => $this->name ) );
		return $currentSelect;
	}

	public function render($row) {
		$value = @$row [$this->key . "_str"];
		$html = $value;
		return $html;
	}
}