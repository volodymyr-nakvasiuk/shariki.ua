<?php
class Crud_Grid_Partners extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'partners_id';
	public $sort = "partners_order";
	public $direction = "ASC";
	public $editAction = 'partners';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Партнеры';

		//$this->formClass = 'Crud_Form_ExtJs_News';

		$this->gridActionName = 'partners';
		$this->table = "Db_Partners";
		$this->fields = array(
			'partners_id' => new ArOn_Crud_Grid_Column_Default("Id",null,true,false,'50'),
			'partners_logo' => new ArOn_Crud_Grid_Column_Image('Логотип',null,true,false,'100','/uploads/images/partners/{partners_id}/small/{partners_logo}'),
			'partners_url' => new ArOn_Crud_Grid_Column_Default("Ссылка",null,true,false,'100'),
			'partners_title' => new ArOn_Crud_Grid_Column_Default("Название",null,true,false,'100'),
			'partners_order' => new ArOn_Crud_Grid_Column_Default("Сортировка",null,true,false,'100'),
		);

		$this->filters->setPrefix(false);
		$this->filters->fields = array(
			'search' => new ArOn_Crud_Grid_Filter_Field_Search('search','Search:', 
				array(
					ArOn_Db_Filter_Search::ID => ArOn_Db_Filter_Search::EQ,
					ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE,
					'partners_url' => ArOn_Db_Filter_Search::LIKE,
				)
			)
		);
		parent::init();
	}
}
