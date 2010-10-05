<?php
class ArOn_Crud_Grid_Column_Numeric extends ArOn_Crud_Grid_Column_Default {

	protected $type = 'float';

	public $decimals = 0;

	function __construct($title, $name = null, $isSort = true, $hidden = false, $width = 80, $render_function = false, $decimals = 0) {
		parent::__construct ( $title, $name, $isSort ,$hidden, $width, $render_function);
		$this->decimals = $decimals;
	}

	public function render(array &$row) {
		$value = number_format ( (float)$row [$this->key], $this->decimals, '.', '');
		if ($this->link) {
			$value = $this->createActionLink ( $value, @$row [$this->gridTitleField] );
		}
		return $value;
	}

}