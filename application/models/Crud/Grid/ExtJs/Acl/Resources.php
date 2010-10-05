<?php
class Crud_Grid_ExtJs_Acl_Resources extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'acl_resource_id';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Ресурсы';

		$this->gridActionName = 'acl-resources';
		$this->table = "Db_AclResources";
		$this->fields = array(
			'acl_resource_id' => new ArOn_Crud_Grid_Column_Numeric('Id',null,true,false,'20'),
			'acl_module_id' => new ArOn_Crud_Grid_Column_JoinOne('Модуль','Db_AclModules', null, null, false, '50'),
			'acl_resource_name' => new ArOn_Crud_Grid_Column_Default("Название",null,true,false,'50'),
			'privileges' => new ArOn_Crud_Grid_Column_JoinMany('Привелегии','Db_AclPrivileges',null,null,', ',5, '100')
		);
		$this->fields['privileges']->setAction ('acl-privileges','parent');

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
											),
										array(
												'path' => array('Db_AclModules'),
												'filters' => array(
																ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
															),
												)
										)
					),
					'id' => new ArOn_Crud_Grid_Filter_Field_Value('acl_resource_id', 'id:',ArOn_Db_Filter_Field::EQ),
		            'name' => new ArOn_Crud_Grid_Filter_Field_Text('acl_resource_name','Название:'), 
		            'parent' => new ArOn_Crud_Grid_Filter_Field_Select2('acl_module_id','Модуль:', 'Db_AclModules'),
		);

		parent::init();
	}
}
