<?php
class Crud_Grid_ExtJs_Seo extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'seo_id';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'СЕО тексты сайта';

		$this->gridActionName = 'seo';
		$this->table = "Db_Seo";
		$this->fields = array(
			'seo_id' => new ArOn_Crud_Grid_Column_Numeric('Id',null,true,false,'20'),
			'seo_module_id' => new ArOn_Crud_Grid_Column_JoinOne('Модуль',array('Db_SiteActs','Db_SiteController','Db_SiteModule'), null, null, false, '100'),
			'seo_controller_id' => new ArOn_Crud_Grid_Column_JoinOne('Контроллер',array('Db_SiteActs','Db_SiteController'), null, null, false, '100'),
			'seo_action_id' => new ArOn_Crud_Grid_Column_JoinOne('Экшн','Db_SiteActs', null, null, false, '100'),
			'seo_title' => new ArOn_Crud_Grid_Column_Default("Заголовок",null,false,false,'200'),
			'seo_keywords' => new ArOn_Crud_Grid_Column_Default("Ключевые слова",null,false,false,'200'),
			'seo_description' => new ArOn_Crud_Grid_Column_Default("Описани сайта",null,false,false,'200'),
			'seo_text' => new ArOn_Crud_Grid_Column_Default("СЕО текст",null,false,true,'200'),
		);

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
															'path' => array('Db_SiteActs'),
															'filters' => array(
																		ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
																		)
											),
									array(
															'path' => array('Db_SiteActs','Db_SiteController'),
															'filters' => array(
																		ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
																		)
											),
									array(
															'path' => array('Db_SiteActs','Db_SiteController','Db_SiteModule'),
															'filters' => array(
																		ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
																		)
											)
									)
		),
					'id' => new ArOn_Crud_Grid_Filter_Field_Value('action_id', 'id:',ArOn_Db_Filter_Field::EQ),
					'seoaction' => new ArOn_Crud_Grid_Filter_Field_Select2('action_id','Экшн:', 'Db_SiteActs'),
		);

		parent::init();
	}
}
