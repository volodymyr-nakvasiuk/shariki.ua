<?php
class Crud_Grid_AclUsers extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'id';
	public $sort = "name";
	public $direction = "ASC";

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'пользователи';
		$this->gridActionName = 'acl-users';

		$this->table = "Db_AclUsers";
		$this->fields = array(
			'id' => new Crud_Column_Default('ID'),
			'name' => new Crud_Column_Default('Name'),
			'group' => new Crud_Column_JoinOne('Group', 'Db_AclGroups'),               
			'password' => new Crud_Column_Default('Password'),
			'enabled' => new Crud_Column_FormColumn('Enabled'),
			'formal_name' => new Crud_Column_Default('Formal Name'),
			
			'rules' => new Crud_Column_JoinMany('Rules', array('Db_AclUserRules', 'Db_AclRules'), null, null, "<br>"),			
			'clients' => new Crud_Column_JoinMany('Клиенты', array('Db_AclUserClients', 'Db_Client'), null, null, "<br>"),			
		);
			
		$this->fields['group']->link = '/'.self::$ajaxModuleName.'/acl-groups/?id={value}';

		$this->fields['rules']->link = '/'.self::$ajaxModuleName.'/acl-rules/?filter[user_id]={value}';

		//		$this->fields['affiliates']->link = '/'.self::$ajaxModuleName.'/affiliates/?filter[user_id]={value}';

		$this->filters->fields = array(
			'name' => new Crud_Filter_Field_Text('name','Name:'),
			'groups' => new Crud_Filter_Field_Select2('group_id','Groups:', 'Db_AclGroups'),						 					  
			'affiliates' => new Crud_Filter_Field_Select2('affiliate_id','Affiliates:', 'Db_Affiliates', array('Db_AclUserAffiliates', 'Db_Affiliates')),
			'rules' => new Crud_Filter_Field_Select2('rule_id','Rules', 'Db_AclRules', array('Db_AclUserRules', 'Db_AclRules'))
		);

		parent::init();
	}
}
