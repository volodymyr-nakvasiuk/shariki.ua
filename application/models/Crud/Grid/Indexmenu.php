<?php
class Crud_Grid_Indexmenu extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'indexmenu_id';
	public $sort = "indexmenu_order";
	public $direction = "ASC";

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Меню на главной';

		$this->gridActionName = 'indexmenu';
		$this->table = "Db_Indexmenu";
		$this->fields = array(
			'indexmenu_id' => new ArOn_Crud_Grid_Column_Default("Id",null,true,false,'50'),
			'indexmenu_url' => new ArOn_Crud_Grid_Column_Default("Ссылка",null,true,false,'150'),
			'indexmenu_title' => new ArOn_Crud_Grid_Column_Default("Подпись",null,true,false,'150'),
			'indexmenu_order' => new ArOn_Crud_Grid_Column_Default("Сортировка",null,true,false,'150'),
		);
		
		$this->filters->setPrefix(false);
		$this->filters->fields = array(
			'search' => new ArOn_Crud_Grid_Filter_Field_Search('search','Search:', 
				array(
					ArOn_Db_Filter_Search::ID => ArOn_Db_Filter_Search::EQ,
					ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE,
					'indexmenu_url' => ArOn_Db_Filter_Search::LIKE,
				)
			)
		);

		parent::init();
	}
}
