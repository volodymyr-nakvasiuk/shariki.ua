<?php
class Crud_Grid_Servicesgallery extends ArOn_Crud_Grid {

	protected $_idProperty = 'photos_id';
	public $sort = "photos_order";
	public $direction = "ASC";
	public $editAction = 'view-services-gallery-form';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Галерея';

		$this->ajaxActionName = 'servicesgallery';
		$this->table = "Db_View_ServicesPhotos";
		$this->fields = array(
			'photos_id' => new ArOn_Crud_Grid_Column_Numeric('Id',null,true,false,'50'),
			'photos_name' => new ArOn_Crud_Grid_Column_Image('Изображение',null,true,false,'100','/uploads/images/services/{photos_parent_id}/small/{photos_name}'),
			'photos_title' =>  new ArOn_Crud_Grid_Column_Default("Описание",null,true,false,'100'),
			'photos_main' =>  new ArOn_Crud_Grid_Column_Numeric("Главная",null,true,false,'100'),
			'photos_order' =>  new ArOn_Crud_Grid_Column_Numeric("Сортировка",null,true,false,'100'),
			'photos_parent_id' => new ArOn_Crud_Grid_Column_Numeric(false,null,true,false,'0'),
		);
		$this->filters->setPrefix(false);
		$this->filters->fields = array(
			'parent_id' => new ArOn_Crud_Grid_Filter_Field_Text('photos_parent_id','Родитель:',ArOn_Db_Filter_Field::EQ), 
		);
		parent::init();
	}
}
