<?php
class Crud_Grid_ExtJs_Feedback extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'feedback_id';
	public $sort = "feedback_order";
	public $direction = "ASC";
	public $editAction = 'feedback';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Отзывы';

		$this->gridActionName = 'feedback';
		$this->table = "Db_Feedback";
		$this->fields = array(
			'feedback_id' => new ArOn_Crud_Grid_Column_Default("Id",null,true,false,'50'),
			'feedback_title' => new ArOn_Crud_Grid_Column_Default("Название",null,true,false,'100'),
			'feedback_order' => new ArOn_Crud_Grid_Column_Default("Сортировка",null,true,false,'100'),
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
