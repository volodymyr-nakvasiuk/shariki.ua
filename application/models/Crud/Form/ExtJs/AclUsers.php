<?php
class Crud_Form_ExtJs_AclUsers extends ArOn_Crud_Form_ExtJs {
	protected $modelName = 'Db_AclUsers';

	public function init(){

		$this->action = '/' . self::$ajaxModuleName . '/acl-users/save/';
		$this->actionName = 'acl-users';

		$this->fields = array(
		 					  'name' => new ArOn_Crud_Form_Field_Text('name','Name:'),                         
		 					  'password' => new ArOn_Crud_Form_Field_Text('password','Password:'),
		 					  'group' => new ArOn_Crud_Form_Field_Many2One('group_id','Group:'),
		 					  'enabled' => new ArOn_Crud_Form_Field_Checkbox('enabled','Enabled'),
		 					  'formal_name' => new ArOn_Crud_Form_Field_Text('formal_name','Formal Name:'),
		 					  'info' => new ArOn_Crud_Form_Field_TextArea('info','Info:'),
			
		 					  'client' => new ArOn_Crud_Form_Field_Many2Many('client','Пользователи:'),
		 					  'rules' => new ArOn_Crud_Form_Field_Many2Many('rules','Rules:')	 					 

		);

		$this->fields['group']->helper = array(
	     										'model' => 'Db_AclGroups',
		);

		$this->fields['rules']->helper = array(
	     										'model' => 'Db_AclRules',
	     										'workingModel' => 'Db_AclUserRules',
		);

		$this->fields['client']->helper = array(
	     										'model' => 'Db_Client',
	     										'workingModel' => 'Db_AclUserClients',
		);
		 
		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');

		$this->groups = array(
	         				'Form' => array_keys($this->fields)
		);
		 

		parent::init();
	}
}
