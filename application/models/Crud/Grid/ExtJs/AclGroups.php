<?php
class Crud_Grid_AclGroups extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'id';
	public $sort = "name";
	public $direction = "ASC";

	public function init() {

		$this->trash = false;
		$this->gridTitle = 'Группы';
		$this->gridActionName = 'acl-groups';
		$this->table = "Db_AclGroups";

		$this->fields = array(
			'id' => new ArOn_Crud_Grid_Column_Default('ID'),
			'name' => new ArOn_Crud_Grid_Column_Default('Name'),
			'rules' => new ArOn_Crud_Grid_Column_JoinMany('Rules', array('Db_AclGroupRules', 'Db_AclRules'), null, null, "<br>"),
			'users' => new ArOn_Crud_Grid_Column_JoinMany('Admins', 'Db_AclUsers', null, null, "<br>")
		);
		//$this->fields['rules']->link = '/'.self::$ajaxModuleName.'/acl-rules/?filter[group_id]={value}';
		//$this->fields['users']->link =  '/'.self::$ajaxModuleName.'/acl-users/?filter[group_id]={value}';
		$this->filters->fields = array(
			'name' => new ArOn_Crud_Grid_Filter_Field_Text('name','Name:'),
			'rules' => new ArOn_Crud_Grid_Filter_Field_Select2('rule_id','Rules', 'Db_AclRules', array('Db_AclGroupRules', 'Db_AclRules'))
		);

		parent::init();
	}
}
