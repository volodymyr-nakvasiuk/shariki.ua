<?php
class Crud_Grid_AclRules extends ArOn_Crud_Grid_ExtJs {

	protected $_idProperty = 'id';
	public $sort = "name";
	public $direction = "ASC";

	public function init() {
		$this->trash = false;
		$this->gridTitle = 'Правила';
		$this->gridActionName = 'acl-rules';
		$this->table = "Db_AclRules";

		$this->fields = array(
			'id' => new Crud_Column_Default('ID'),
			'name' => new Crud_Column_Default('Name'),
			'description' => new Crud_Column_Default('Description'),               
		);
			
		$this->filters->fields = array(
			'name' => new Crud_Filter_Field_Text('name','Name:'), 
			'permission' => new Crud_Filter_Field_Array2Select('perm','Permission:'),
			'type' => new Crud_Filter_Field_Array2Select('type','Type:'),
			'groups'=> new Crud_Filter_Field_Select2('group_id','Groups:', 'Db_AclGroups', array('Db_AclGroupRules', 'Db_AclGroups')),
			'users'=> new Crud_Filter_Field_Select2('user_id','Users:', 'Db_AclUsers', array('Db_AclUserRules', 'Db_AclUsers'))
		);
		$this->filters->fields['permission']->options = Zend_Registry::get('perm');
		$this->filters->fields['type']->options = Zend_Registry::get('type');

		parent::init();
	}
}
