<?php
class Crud_Grid_ExtJs_SiteModules extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'module_id';
	public $sort = "module_name";
	public $direction = "ASC";

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Модули сайта';

		$this->gridActionName = 'site-modules';
		$this->table = "Db_SiteModule";
		$this->fields = array(		
			'module_id' => new ArOn_Crud_Grid_Column_Numeric('Id',null,true,false,'20'),			
			'module_name' => new ArOn_Crud_Grid_Column_Default("Alt имя",null,true,false,'50'),
			'controllers' => new ArOn_Crud_Grid_Column_JoinMany('Контроллеры','Db_SiteController',null,null,', ',9, '150'),
			'module_status' => new ArOn_Crud_Grid_Column_Array("Статус",null,true,false,'50')
			//'menu_role' => new ArOn_Crud_Grid_Column_JoinOne('Роль','Db_AclRole',null,null,', ',9, '150')
		);
		$this->fields['controllers']->setAction ('site-controllers','site-modules');
		$this->fields['module_status']->options = array('active' => "Активный", 'not_active' => "Не активный");
		$this->filters->setPrefix(false);
		$this->filters->fields = array(
			'search' => new ArOn_Crud_Grid_Filter_Field_Search('search','Search:', 
								array(
									ArOn_Db_Filter_Search::ID => ArOn_Db_Filter_Search::EQ,
									ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
									//'price_description' => ArOn_Db_Filter_Search::LIKE
								)
		),
			'id' => new ArOn_Crud_Grid_Filter_Field_Value('module_id', 'id:',ArOn_Db_Filter_Field::EQ)
		);

		parent::init();
	}
}
