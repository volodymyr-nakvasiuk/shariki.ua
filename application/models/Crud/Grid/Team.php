<?php
class Crud_Grid_Team extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'team_id';
	public $sort = "team_order";
	public $direction = "ASC";
	public $editAction = 'team';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Наша команда';

		$this->gridActionName = 'team';
		$this->table = "Db_Team";
		$this->fields = array(
			'team_id' => new ArOn_Crud_Grid_Column_Default("Id",null,true,false,'50'),
			'team_photo' => new ArOn_Crud_Grid_Column_Image('Фото',null,true,false,'100','/uploads/images/team/{team_id}/small/{team_photo}'),
			'team_name' => new ArOn_Crud_Grid_Column_Default("Имя",null,true,false,'100'),
			'team_title' => new ArOn_Crud_Grid_Column_Default("Должность",null,true,false,'100'),
			'team_order' => new ArOn_Crud_Grid_Column_Default("Сортировка",null,true,false,'100'),
		);

		$this->filters->setPrefix(false);
		$this->filters->fields = array(
			'search' => new ArOn_Crud_Grid_Filter_Field_Search('search','Search:', 
				array(
					ArOn_Db_Filter_Search::ID => ArOn_Db_Filter_Search::EQ,
					ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
				)
			)
		);

		parent::init();
	}
}
