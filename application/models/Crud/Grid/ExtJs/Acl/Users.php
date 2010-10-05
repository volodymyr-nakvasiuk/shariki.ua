<?php
class Crud_Grid_ExtJs_Acl_Users extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'id';
	public $editController = 'form';
	public $editAction = 'acl-users';
	
	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Пользователи';
		$this->gridActionName = 'acl-users';
		$this->table = "Db_AclUsers";
		
		$this->fields = array(
			'id' => new ArOn_Crud_Grid_Column_Default('ID',null,true,false,'50'),
			'name' => new ArOn_Crud_Grid_Column_Default('Логин',null,true,false,'50'),
			'role' => new ArOn_Crud_Grid_Column_JoinOne('Роль', 'Db_AclRoles'),
			//'enabled' => new ArOn_Crud_Grid_Column_FormColumn('Статус',null,true,false,'50'),
			'enabled' => new ArOn_Crud_Grid_Column_Default('Статус',null,true,false,'50'),
			'formal_name' => new ArOn_Crud_Grid_Column_Default('Formal Name',null,true,false,'50'),			
		);

		//		$this->fields['affiliates']->link = '/'.self::$ajaxModuleName.'/affiliates/?filter[user_id]={value}';

		$this->filters->fields = array(
			'name' => new ArOn_Crud_Grid_Filter_Field_Text('name','Name:'),
			'role' => new ArON_Crud_Grid_Filter_Field_Select2('acl_role_id', 'Роли', 'Db_AclRoles')
		);

		parent::init();
	}
}
