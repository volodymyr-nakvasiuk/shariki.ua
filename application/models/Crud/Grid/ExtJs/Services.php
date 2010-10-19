<?php
class Crud_Grid_ExtJs_Services extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'services_id';
	public $sort = "services_order";
	public $direction = "ASC";
	public $editController = 'grid';
	public $editAction = 'services-tab';

	public function init() {
		$this->trash = true;
		$this->gridTitle = 'Услуги';

		$this->formClass = 'Crud_Form_ExtJs_Services';

		$this->gridActionName = 'services';
		$this->table = "Db_Services";
		$this->fields = array(
			'services_id' => new ArOn_Crud_Grid_Column_Default("Id",null,true,false,'50'),
			'services_photo' => new ArOn_Crud_Grid_Column_Image('Изображение',null,true,false,'100','/uploads/images/services/{services_id}/small/{services_photo}'),
			'services_title' => new ArOn_Crud_Grid_Column_Default("Название",null,true,false,'100'),
			'services_order' =>  new ArOn_Crud_Grid_Column_FormColumnExtJs("Сортировка", 'services_order', true, array(), '50'),
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
