<?php
class Crud_Grid_ExtJs_SiteActs extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'action_id';
	public $sort = "action_controller_id";
	public $direction = "ASC";

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Экшины сайта';

		$this->gridActionName = 'site-acts';
		$this->table = "Db_SiteActs";
		$this->fields = array(
			'action_id' => new ArOn_Crud_Grid_Column_Numeric('Id',null,true,false,'20'),
			'action_module_id' => new ArOn_Crud_Grid_Column_JoinOne('Модуль', array('Db_SiteController','Db_SiteModule'), null, null, false, '50'),
			'action_controller_id' => new ArOn_Crud_Grid_Column_JoinOne('Контроллер','Db_SiteController', null, null, false, '50'),
			'action_name' => new ArOn_Crud_Grid_Column_Default("Alt имя",null,true,false,'50'),
			'action_status' => new ArOn_Crud_Grid_Column_Array("Статус",null,true,false,'50')
		);

		$this->fields['action_status']->options = array('active' => "Активный", 'not_active' => "Не активный");
			
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
															'path' => array('Db_SiteController'),
															'filters' => array(
																		ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
																		)
											),
									array(
															'path' => array('Db_SiteController','Db_SiteModule'),
															'filters' => array(
																		ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
																		)
											)
									)
		),
					'id' => new ArOn_Crud_Grid_Filter_Field_Value('action_id', 'id:',ArOn_Db_Filter_Field::EQ),
					'sitemodule' => new ArOn_Crud_Grid_Filter_Field_Select2('module_id','Модули:', 'Db_SiteModule',array('Db_SiteController','Db_SiteModule')),
               		'sitecontroller' => new ArOn_Crud_Grid_Filter_Field_Select2('controller_id','Контроллеры:', 'Db_SiteController'),
					'modulename' => new ArOn_Crud_Grid_Filter_Field_Select2('module_name','Модули:', 'Db_SiteModule',array('Db_SiteController','Db_SiteModule')),
            		'controllername' => new ArOn_Crud_Grid_Filter_Field_Select2('controller_name','Контроллеры:', 'Db_SiteController'),
					'actionname' => new ArOn_Crud_Grid_Filter_Field_Value('action_name', 'Экшн:',ArOn_Db_Filter_Field::EQ),
		);

		parent::init();
	}
}
