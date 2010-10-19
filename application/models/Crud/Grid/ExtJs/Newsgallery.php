<?php
class Crud_Grid_ExtJs_Newsgallery extends ArOn_Crud_Grid_ExtJs_GalleryGrid {

	protected $_idProperty = 'photos_id';
	public $sort = "photos_order";
	public $direction = "ASC";
	
	public $editAction = 'view-news-gallery-form';

	public function init() {
		$this->trash = false;
		$this->formClass = 'Crud_Form_ExtJs_Newsgallery';
		$this->gridTitle = 'Галерея';

		$this->ajaxActionName = 'newsgallery';
		$this->table = "Db_View_NewsPhotos";
		$this->fields = array(
			'photos_id' => new ArOn_Crud_Grid_Column_Numeric('Id',null,true,false,'50'),
			'photos_name' => new ArOn_Crud_Grid_Column_Image('Изображение',null,true,false,'100','/uploads/images/news/{photos_parent_id}/small/{photos_name}'),
			'photos_title' =>  new ArOn_Crud_Grid_Column_FormColumnExtJs("Описание", 'photos_title', false, array(), '150'),
			'photos_main' =>  new ArOn_Crud_Grid_Column_FormColumnExtJs("Главная", 'photos_main', true, array(), '50'),
			'photos_order' =>  new ArOn_Crud_Grid_Column_FormColumnExtJs("Сортировка", 'photos_order', true, array(), '50'),
			'photos_parent_id' => new ArOn_Crud_Grid_Column_Numeric(false,null,true,false,'0'),
		);
		$this->filters->setPrefix(false);
		$this->filters->fields = array(
			'parent_id' => new ArOn_Crud_Grid_Filter_Field_Text('photos_parent_id','Родитель:',ArOn_Db_Filter_Field::EQ), 
		);
		parent::init();
	}
}
