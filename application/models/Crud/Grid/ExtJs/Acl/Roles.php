<?php
class Crud_Grid_ExtJs_Acl_Roles extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'acl_role_id';
	public $sort = "acl_role_id";
	public $direction = "DESC";
	public $editController = 'form';
	public $editAction = 'acl-roles';
	
	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Роли';

		$this->gridActionName = 'acl-roles';
		$this->table = "Db_AclRoles";
		$this->fields = array(
			'acl_role_id' => new ArOn_Crud_Grid_Column_Numeric('Id',null,true,false,'20'),
			'acl_role_name' => new ArOn_Crud_Grid_Column_Default("Название",null,true,false,'50'),
			'privileges' => new ArOn_Crud_Grid_Column_JoinMany('Привилегия',array('Db_AclRolePrivileges','Db_AclPrivileges'),null,null,', ',5, '100')
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
												'path' => array('Db_AclRolePrivileges','Db_AclPrivileges'),
												'filters' => array(
																ArOn_Db_Filter_Search::NAME => ArOn_Db_Filter_Search::LIKE
															),
												)
										)
					),
					'id' => new ArOn_Crud_Grid_Filter_Field_Value('acl_role_id', 'id:',ArOn_Db_Filter_Field::EQ),
		            'name' => new ArOn_Crud_Grid_Filter_Field_Text('acl_role_name','Название:'),
					'parent' => new ArOn_Crud_Grid_Filter_Field_Select2('acl_privilege_id','Привилегии:', 'Db_AclPrivileges',array('Db_AclRolePrivileges','Db_AclPrivileges'))
		);

		parent::init();
	}
}
