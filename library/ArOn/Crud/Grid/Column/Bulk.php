<?php
class ArOn_Crud_Grid_Column_Bulk extends ArOn_Crud_Grid_Column_FormCheckbox {

	public function __construct() {
		parent::__construct ( '', 0 );
		$this->class = 'grid-bulk-action';
		$attr = '';
		$this->title = '<input type="checkbox" class="grid-bulk-action-all" ' . $attr . '/>';
	}

	public function init($grid, $key) {
		$this->method = $grid->ajaxActionName;
		parent::init ( $grid, $key );
	}

}