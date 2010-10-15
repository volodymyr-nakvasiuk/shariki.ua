<?php
class Crud_Grid_Marketc extends ArOn_Crud_Grid {

	protected $_idProperty = 'marketc_id';
	public $sort = "marketc_order";
	public $direction = "ASC";
	public $editController = 'form';
	public $editAction = 'marketc';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Розничная торговля - Категории';

		$this->gridActionName = 'marketc';
		$this->table = "Db_Marketc";
		
		$this->fields = array(
			'marketc_id' => new ArOn_Crud_Grid_Column_Default("Id",null,true,false,'50'),
			'marketc_img' => new ArOn_Crud_Grid_Column_Image('Изображение',null,true,false,'100','/uploads/images/market_cat/{marketc_id}/small/{marketc_img}'),
			'marketc_title' => new ArOn_Crud_Grid_Column_Default("Название",null,true,false,'100'),
			'marketc_text' => new ArOn_Crud_Grid_Column_Default("Описание категории",null,true,false,'100'),
			'marketc_order' => new ArOn_Crud_Grid_Column_Default("Сортировка",null,true,false,'50'),
			'marketd' => new ArOn_Crud_Grid_Column_JoinMany('Товары','Db_Marketd',null,null,', ',5, '100')
		);
		$this->fields['marketd']->setAction ('marketd','parent');

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
