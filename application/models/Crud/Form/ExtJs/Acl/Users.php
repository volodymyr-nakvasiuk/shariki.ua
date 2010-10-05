<?php
class Crud_Form_ExtJs_Acl_Users extends ArOn_Crud_Form_ExtJs {
	
	protected $modelName = 'Db_AclUsers';
	protected $_title = 'Пользователи';
	
	public function init(){

		$this->action = '/' . self::$ajaxModuleName . '/acl-users/save/';
		$this->actionName = 'acl-users';

		$this->fields = array(
							  'id' => new ArOn_Crud_Form_Field_Numeric('id', 'Id', null, true) ,
		 					  'name' => new ArOn_Crud_Form_Field_Text('name','Логин'),                         
		 					  'password' => new ArOn_Crud_Form_Field_Text('password','Пароль'),
		 					  'role' => new ArOn_Crud_Form_Field_Many2One('role_id','Роль'),
		 					  'enabled' => new ArOn_Crud_Form_Field_Checkbox('enabled','Статус'),
		 					  'formal_name' => new ArOn_Crud_Form_Field_Text('formal_name','Formal Name'),
		 					  'info' => new ArOn_Crud_Form_Field_Text('info','Информация')

		);

		$this->fields['role']->helper = array(
	     										'model' => 'Db_AclRoles',
		);
		 
		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');

		$this->groups = array(
	         				'Form' => array_keys($this->fields)
		);
		 

		parent::init();
	}
}
