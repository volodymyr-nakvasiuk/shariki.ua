<?php
class Crud_Grid_ExtJs_Calendar extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'calendar_id';
	public $sort = "calendar_date";
	public $direction = "DESC";
	public $editAction = 'calendar';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Календарь событий';

		$this->gridActionName = 'calendar';
		$this->table = "Db_Calendar";
		$this->fields = array(
			'calendar_id' => new ArOn_Crud_Grid_Column_Default("Id",null,true,false,'50'),
			'calendar_photo' => new ArOn_Crud_Grid_Column_Image('Изображение',null,true,false,'100','/uploads/images/calendar/{calendar_id}/small/{calendar_photo}'),
			'calendar_title' => new ArOn_Crud_Grid_Column_Default("Название",null,true,false,'100'),
			'calendar_description' => new ArOn_Crud_Grid_Column_Default("Описание",null,true,false,'100'),
			'calendar_date' => new ArOn_Crud_Grid_Column_Default("Дата",null,true,false,'100'),
		);

		$this->filters->setPrefix(false);
		$this->filters->fields = array(
			'search' => new ArOn_Crud_Grid_Filter_Field_Search('search','Search:', 
				array(
					ArOn_Db_Filter_Search::ID => ArOn_Db_Filter_Search::EQ,
					ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE,
					'calendar_date' => ArOn_Db_Filter_Search::LIKE
				)
			)
		);

		parent::init();
	}
}
