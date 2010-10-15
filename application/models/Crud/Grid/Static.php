<?php
class Crud_Grid_Static extends ArOn_Crud_Grid {

	protected $_idProperty = 'static_id';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Статические страницы сайта';

		$this->gridActionName = 'static';
		$this->table = "Db_Static";
		$this->fields = array(
			'static_id' => new ArOn_Crud_Grid_Column_Numeric('Id',null,true,false,'20'),
			'static_module_id' => new ArOn_Crud_Grid_Column_JoinOne('Модуль',array('Db_SiteActs','Db_SiteController','Db_SiteModule'), null, null, false, '100'),
			'static_controller_id' => new ArOn_Crud_Grid_Column_JoinOne('Контроллер',array('Db_SiteActs','Db_SiteController'), null, null, false, '100'),
			'static_action' => new ArOn_Crud_Grid_Column_JoinOne('Экшн','Db_SiteActs', null, null, false, '100'),
			'static_title' => new ArOn_Crud_Grid_Column_Default("Заголовок",null,false,false,'200'),
			'static_text' => new ArOn_Crud_Grid_Column_Default("Контент",null,false,true,'200'),
		);

		$this->filters->setPrefix(false);
		$this->filters->fields = array(
			'search' => new ArOn_Crud_Grid_Filter_Field_Search('search','Search:', 
				array(
					ArOn_Db_Filter_Search::ID => ArOn_Db_Filter_Search::EQ,
					ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE,
				)
			),
			'staticaction' => new ArOn_Crud_Grid_Filter_Field_Select2('action_id','Экшн:', 'Db_SiteActs'),
		);

		parent::init();
	}
}
