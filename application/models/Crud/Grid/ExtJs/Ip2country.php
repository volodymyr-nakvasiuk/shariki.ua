<?php
class Crud_Grid_ExtJs_Ip2country extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'IP_FROM';
	public $sort = "COUNTRY";
	public $direction = "ASC";

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Страны';

		$this->gridActionName = 'ip2country';
		$this->table = "Db_Ip2Country";
		$this->fields = array(
		/*'icons' => new Crud_Column_Compound(array(
		 'action' => new Crud_Column_Bulk,
		 'edit' => new Crud_Column_Edit,
			), '&nbsp;'),*/
			'IP_FROM' => new ArOn_Crud_Grid_Column_Numeric('Ip начало',null,true,false,'120'),			
			'IP_TO' => new ArOn_Crud_Grid_Column_Numeric("Ip конец",null,true,false,'120'),
			'REGISTRY' => new ArOn_Crud_Grid_Column_Default('REGISTRY',null,true,false,'50'),
			'CTRY' => new ArOn_Crud_Grid_Column_Default('CTRY',null,true,false,'50'),
			'CNTRY' => new ArOn_Crud_Grid_Column_Default('CNTRY',null,true,false,'50'),
			'COUNTRY' => new ArOn_Crud_Grid_Column_Default('Страна',null,true,false,'100'),
		/*'create_date' => new Crud_Column_Date('Create date'),
			'update_date' => new Crud_Column_Date('Update date'),

			'status' => new Crud_Column_FormColumn('Status'),*/

		);
		/*$this->filters->fields = array(
		 'mark_name' => new ArOn_Crud_Grid_Filter_Field_Text('mark_name','Название:'),
		 'model_id' => new ArOn_Crud_Grid_Filter_Field_Select2('mark_id','Модель:', 'Db_EModel'),
		 );*/
		
		$this->filters->setPrefix(false);
		$this->filters->fields = array(
			'search' => new ArOn_Crud_Grid_Filter_Field_Search('search','Search:', 
					array(
							ArOn_Db_Filter_Search::ID => ArOn_Db_Filter_Search::EQ,
							ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE,
							'CTRY',
							'CNTRY'
							
					//'price_description' => ArOn_Db_Filter_Search::LIKE
					)
			),
			//'id' => new ArOn_Crud_Grid_Filter_Field_Value('mark_id', 'id:',ArOn_Db_Filter_Field::EQ)
		);

		parent::init();
	}
}
