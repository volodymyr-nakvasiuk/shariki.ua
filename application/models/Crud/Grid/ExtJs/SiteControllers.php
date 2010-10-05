<?php
class Crud_Grid_ExtJs_SiteControllers extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'controller_id';
	public $sort = "controller_module_id";
	public $direction = "ASC";

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Контроллеры сайта';

		$this->gridActionName = 'site-controllers';
		$this->table = "Db_SiteController";
		$this->fields = array(
			'controller_id' => new ArOn_Crud_Grid_Column_Numeric('Id',null,true,false,'20'),
			'controller_module_id' => new ArOn_Crud_Grid_Column_JoinOne('Меню','Db_SiteModule', null, null, false, '50'),
			'controller_name' => new ArOn_Crud_Grid_Column_Default("Alt имя",null,true,false,'50'),
			'actions' => new ArOn_Crud_Grid_Column_JoinMany('Экшины','Db_SiteActs',null,null,', ',9, '150'),
			'controller_status' => new ArOn_Crud_Grid_Column_Array("Статус",null,true,false,'50')
		);

		$this->fields['actions']->setAction ('site-acts','site-controllers');
		$this->fields['controller_status']->options = array('active' => "Активный", 'not_active' => "Не активный");
			
		$this->filters->setPrefix(false);		
		$this->filters->fields = array(
					'search' => new ArOn_Crud_Grid_Filter_Field_Search('search','Search:', array(
									array(
															'path' => null,
															'filters' => array(
																		ArOn_Db_Filter_Search::ID => ArOn_Db_Filter_Search::EQ,
																		ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
																		),
											),
									array(
															'path' => array('Db_SiteModule'),
															'filters' => array(
																		ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
																		)
											)
									)
		),
					'id' => new ArOn_Crud_Grid_Filter_Field_Value('controller_id', 'id:',ArOn_Db_Filter_Field::EQ),
               		'site-modules' => new ArOn_Crud_Grid_Filter_Field_Select2('controller_module_id','Модули:', 'Db_SiteModule')
		);

		parent::init();
	}
}
