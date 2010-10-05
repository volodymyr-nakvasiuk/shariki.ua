<?php
class Crud_Grid_ExtJs_Client extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'client_id';
	public $sort = "client_name";
	public $direction = "ASC";

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Пользователи';

		$this->gridActionName = 'client';
		$this->table = "Db_Client";
		$this->fields = array(
		/*'icons' => new Crud_Column_Compound(array(
		 'action' => new Crud_Column_Bulk,
		 'edit' => new Crud_Column_Edit,
			), '&nbsp;'),*/
		 
		 	'client_id' => new ArOn_Crud_Grid_Column_Numeric('Id',null,true,false,'20'),
		 	//'client_photos' => new ArOn_Crud_Grid_Column_Image('Изображение',null,true,false,'100','/catalog/images/generations/cartests/{*}'),			
			'client_name' => new ArOn_Crud_Grid_Column_Default("Имя",null,true,false,'200'),
			//'client_acl_group_id' => new ArOn_Crud_Grid_Column_JoinMany('Права доступа', array('Db_AclUserClients', 'Db_AclUsers'), null, null, "<br>",null,50),
			'client_tel' => new ArOn_Crud_Grid_Column_Default('Телефон',null,true,false,'100'),
		 	'client_email' => new ArOn_Crud_Grid_Column_Default('Почта',null,true,false,'100'),
		 	//'client_url' => new ArOn_Crud_Grid_Column_Default('Сайт',null,true,false,'50'),
		 	'client_region_id' => new ArOn_Crud_Grid_Column_JoinOne('Область','Db_Region',null,null,false,'120'),
		 	//'client_place_id' => new ArOn_Crud_Grid_Column_JoinOne('Город','Db_View_Place'),
		 	//'client_place' => new ArOn_Crud_Grid_Column_Default('Город',null,true,false,'80'),
		 	//'client_addr' => new ArOn_Crud_Grid_Column_Default('Адрес',null,true,false,'50'),
		 	'client_enabled' => new ArOn_Crud_Grid_Column_Default('Активный',null,true,false,'80')
		 
		/*'create_date' => new Crud_Column_Date('Create date'),
			'update_date' => new Crud_Column_Date('Update date'),

			'status' => new Crud_Column_FormColumn('Status'),*/

		);
		/*$this->filters->fields = array(
		 'mark_name' => new ArOn_Crud_Grid_Filter_Field_Text('mark_name','Название:'),
		 'model_id' => new ArOn_Crud_Grid_Filter_Field_Select2('mark_id','Модель:', 'Db_EModel'),
		 );*/
		//$this->fields['models']->setAction ('emodel','parent');

		$this->filters->setPrefix(false);
		$this->filters->fields = array(
			'search' => new ArOn_Crud_Grid_Filter_Field_Search('search','Search:', 
						array(
								ArOn_Db_Filter_Search::ID => ArOn_Db_Filter_Search::EQ,
								ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE,
								'client_email' => ArOn_Db_Filter_Search::LIKE,
								'client_region_id' => ArOn_Db_Filter_Search::LIKE,
								//'price_description' => ArOn_Db_Filter_Search::LIKE
						)
			),
			'id' => new ArOn_Crud_Grid_Filter_Field_Value('client_id', 'id:',ArOn_Db_Filter_Field::EQ)
		);

		parent::init();
	}
}
