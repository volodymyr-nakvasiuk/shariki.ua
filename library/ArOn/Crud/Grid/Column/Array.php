<?php

class ArOn_Crud_Grid_Column_Array extends ArOn_Crud_Grid_Column_Default {

	public $options;
	public $_na = "-";

	public function render($row) {
		$value = $row [$this->key];
		if ($this->link) {
			$value = $this->createActionLink ( $value, @$row [$this->gridTitleField] );
		}
		if (! empty ( $value )) {
			if (isset ( $this->options [$value] )) {
				$value = $this->options [$value];
			}
		} else {
			$value = $this->_na;
		}
		return $value;
	}
	
	public function setEmptyValue($na = '-'){
		$this->_na = $na;
		return $this;
	}
}