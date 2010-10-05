<?php
class ArOn_Crud_Grid_Column_Edit extends ArOn_Crud_Grid_Column_Action {

	public function __construct() {
		parent::__construct ( '<img border="0" src="/images/edit.gif"/>', 'edit/{value}' );
	}

	public function init($grid, $grid_key) {
		parent::init ( $grid, $grid_key );

		$this->class = 'grid-action2';
		//$this->target = '_blank';
		$this->id = '{value}';
		$this->link = '/' . Crud_Grid_Abstract::$ajaxModuleName . '/' . $grid->gridActionName . '/' . $this->link;
		$this->action = $grid->gridActionName . "-edit";
		//		$this->gridTitleField = 'name';


	}

}