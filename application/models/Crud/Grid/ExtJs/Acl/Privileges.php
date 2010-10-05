<?php
class Crud_Grid_ExtJs_Acl_Privileges extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'acl_privilege_id';

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Привилегии';

		$this->gridActionName = 'acl-privileges';
		$this->table = "Db_AclPrivileges";
		$this->fields = array(
			'acl_privilege_id' => new ArOn_Crud_Grid_Column_Numeric('Id',null,true,false,'20'),			
			'acl_privilege_name' => new ArOn_Crud_Grid_Column_Default("Название",null,true,false,'50'),			
			'acl_resource_id' => new ArOn_Crud_Grid_Column_JoinOne('Ресурс','Db_AclResources', null, null, false, '50'),
			'module' => new ArOn_Crud_Grid_Column_JoinOne('Модуль',array('Db_AclResources','Db_AclModules'), null, null, false, '50'),
			'roles' => new ArOn_Crud_Grid_Column_JoinMany('Роли',array('Db_AclRolePrivileges','Db_AclRoles'),null,null,', ',5, '100')
		);
		$this->fields['roles']->setAction ('acl-roles','parent');

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
												'path' => array('Db_AclResources'),
												'filters' => array(
																ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
															),
												),
										array(
												'path' => array('Db_AclRolePrivileges','Db_AclRoles'),
												'filters' => array(
																ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
															),
												),
										array(
												'path' => array('Db_AclResources','Db_AclModules'),
												'filters' => array(
																ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
															),
												)
										)
					),
					'id' => new ArOn_Crud_Grid_Filter_Field_Value('model_id', 'id:',ArOn_Db_Filter_Field::EQ),
		            'name' => new ArOn_Crud_Grid_Filter_Field_Text('model_name','Название:'), 
		            'parent' => new ArOn_Crud_Grid_Filter_Field_Select2('acl_resource_id','Ресурс:', 'Db_AclResources'),
					'role' => new ArOn_Crud_Grid_Filter_Field_Select2('acl_role_id','Роли:', 'Db_AclRoles',array('Db_AclRolePrivileges','Db_AclRoles'))
		);

		parent::init();
	}
}
