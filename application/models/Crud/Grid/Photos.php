<?php
class Crud_Grid_Photos extends ArOn_Crud_Grid {

	protected $_idProperty = 'photos_id';
	public $sort = "photos_order";
	public $direction = "ASC";
	public $editAction = 'photos';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Галерея';

		$this->gridActionName = 'photos';
		$this->table = "Db_Photos";
		$this->fields = array(
			'photos_id' => new ArOn_Crud_Grid_Column_Default("Id",null,true,false,'50'),
			'photos_core' => new ArOn_Crud_Grid_Column_Image('Изображение',null,true,false,'100','/uploads/images/gallery/{photos_id}/small/{photos_core}'),
			'photos_title' => new ArOn_Crud_Grid_Column_Default("Подпись",null,true,false,'100'),
			'photos_order' =>  new ArOn_Crud_Grid_Column_Default("Сортировка",null,true,false,'50'),
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
