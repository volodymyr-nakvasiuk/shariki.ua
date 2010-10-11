<?php
class Crud_Grid_ExtJs_Marketd extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'marketd_id';
	public $sort = "marketd_order";
	public $direction = "ASC";
	public $editController = 'form';
	public $editAction = 'marketd';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Розничная торговля - Товары';

		$this->gridActionName = 'marketd';
		$this->table = "Db_Marketd";
		
		$this->fields = array(
			'marketd_id' => new ArOn_Crud_Grid_Column_Default("Id",null,true,false,'50'),
			'marketc_title' => new ArOn_Crud_Grid_Column_JoinOne("Категория",'Db_Marketc',null, null,false,'100'),
			'marketd_img' => new ArOn_Crud_Grid_Column_Image('Изображение',null,true,false,'100','/uploads/images/market_tov/{marketd_id}/small/{marketd_img}'),
			'marketd_order' => new ArOn_Crud_Grid_Column_Default("Сортировка",null,true,false,'50'),
		);

		$this->filters->setPrefix(false);
		$this->filters->fields = array(
			'search' => new ArOn_Crud_Grid_Filter_Field_Search('search','Search:', 
				array(
					array(
						'path' => null,
						'filters' => array(
							ArOn_Db_Filter_Search::ID => ArOn_Db_Filter_Search::EQ,
							ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
						)
					),
					array(
						'path' => array('Db_Marketc'),
						'filters' => array(
							ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
						)
					),
				)
			)
		);

		parent::init();
	}
}
