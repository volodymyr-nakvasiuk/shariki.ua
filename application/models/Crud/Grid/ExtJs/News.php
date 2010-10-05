<?php
class Crud_Grid_ExtJs_News extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'news_id';
	public $sort = "news_created_date";
	public $direction = "DESC";
	public $editAction = 'news';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Новости';

		//$this->formClass = 'Crud_Form_ExtJs_News';

		$this->gridActionName = 'news';
		$this->table = "Db_News";
		$this->fields = array(
			'news_id' => new ArOn_Crud_Grid_Column_Default("Id",null,true,false,'50'),
			'news_photo' => new ArOn_Crud_Grid_Column_Image('Изображение',null,true,false,'100','/uploads/images/news/{news_id}/small/{news_photo}'),
			'news_title' => new ArOn_Crud_Grid_Column_Default("Название",null,true,false,'100'),
			'news_description' => new ArOn_Crud_Grid_Column_Default("Описание",null,true,false,'100'),
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
