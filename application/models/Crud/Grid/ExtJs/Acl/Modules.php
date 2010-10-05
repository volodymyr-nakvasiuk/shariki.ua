<?php
class Crud_Grid_ExtJs_Acl_Modules extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'acl_module_id';
	public $sort = "acl_module_name";
	public $direction = "ASC";
	public $editController = 'form';
	public $editAction = 'acl-modules';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Модули';

		$this->gridActionName = 'acl-modules';
		$this->table = "Db_AclModules";
		$this->fields = array(
			'acl_module_id' => new ArOn_Crud_Grid_Column_Numeric('Id',null,true,false,'20'),
			'acl_module_name' => new ArOn_Crud_Grid_Column_Default("Название",null,true,false,'50'),
			'resources' => new ArOn_Crud_Grid_Column_JoinMany('Ресурсы','Db_AclResources',null,null,', ',5, '100')
		);
		$this->fields['resources']->setAction ('acl-resources','parent');

		$this->filters->setPrefix(false);

		$this->filters->setPrefix(false);
		$this->filters->fields = array(
					'search' => new ArOn_Crud_Grid_Filter_Field_Search('search','Search:', 
										array(
											array(
												'path' => null,
												'filters' => array(
																ArOn_Db_Filter_Search::ID => ArOn_Db_Filter_Search::EQ,
																ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE,
																),
											)
										)
					),
					'id' => new ArOn_Crud_Grid_Filter_Field_Value('acl_module_id', 'id:',ArOn_Db_Filter_Field::EQ),
		            'name' => new ArOn_Crud_Grid_Filter_Field_Text('acl_module_name','Название:') 
		            
		);

		parent::init();
	}
}
