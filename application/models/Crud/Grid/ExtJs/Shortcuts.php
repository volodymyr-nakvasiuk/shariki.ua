<?php
class Crud_Grid_ExtJs_Shortcuts extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'shortcut_id';
	public $sort = "shortcut_id";
	public $direction = "ASC";

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Ярлыки на рабочем столе';

		$this->gridActionName = 'shortcuts';
		$this->table = "Db_Shortcuts";
		$this->fields = array(
			'shortcut_id' => new ArOn_Crud_Grid_Column_Default("Id",null,true,false,'50'),
			'shortcut_module' => new ArOn_Crud_Grid_Column_Default("Модуль",null,true,false,'150'),
			'shortcut_text' => new ArOn_Crud_Grid_Column_Default("Подпись",null,true,false,'150'),
			'shortcut_icon' => new ArOn_Crud_Grid_Column_Default("Иконка",null,true,false,'150'),
		);
		
		$this->filters->setPrefix(false);
		$this->filters->fields = array(
			'search' => new ArOn_Crud_Grid_Filter_Field_Search('search','Search:', 
		array(
		ArOn_Db_Filter_Search::ID => ArOn_Db_Filter_Search::EQ,
		ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
		//'price_description' => ArOn_Db_Filter_Search::LIKE
		)
		)
		);

		parent::init();
	}
}
