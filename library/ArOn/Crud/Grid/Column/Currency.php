<?php
class ArOn_Crud_Grid_Column_Currency extends ArOn_Crud_Grid_Column_Numeric {

	function __construct($title, $name = null, $isSort = true) {
		parent::__construct ( $title, $name, $isSort );
		$this->decimals = 2;
	}

}