<?php
class Crud_Grid_Marketd extends ArOn_Crud_Grid {

	protected $_idProperty = 'marketd_id';
	public $sort = "marketd_order";
	public $direction = "ASC";
	public $editController = 'form';
	public $editAction = 'marketd';

	public function init() {
		$this->trash = true;
		$this->gridTitle = 'Розничная торговля - Товары';

		$this->gridActionName = 'marketd';
		$this->table = "Db_Marketd";
		
		$this->fields = array(
			'marketd_id' => new ArOn_Crud_Grid_Column_Default("Id",null,true,false,'50'),
			'marketc_title' => new ArOn_Crud_Grid_Column_JoinOne("Категория",'Db_Marketc','marketc_title', null,false,'100'),
			'marketc_descr' => new ArOn_Crud_Grid_Column_JoinOne("Описание внутри категории",'Db_Marketc','marketc_descr',null,false,'100'),
			'marketc_id' => new ArOn_Crud_Grid_Column_JoinOne("Id категории",'Db_Marketc','marketc_id',null,false,'100'),
			'marketc_img' => new ArOn_Crud_Grid_Column_JoinOne("Изображение категории",'Db_Marketc','marketc_img',null,false,'100'),
			'marketd_text' => new ArOn_Crud_Grid_Column_Default("Описание товара",null,true,false,'100'),
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
			),
			'catid' => new ArOn_Crud_Grid_Filter_Field_Value('marketc_id', 'Category',ArOn_Db_Filter_Field::EQ)
		);

		parent::init();
	}
}
