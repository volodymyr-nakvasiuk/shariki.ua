<?php
class Crud_Form_ExtJs_AclGroups extends ArOn_Crud_Form_ExtJs {
	protected $modelName = 'Db_AclGroups';

	public function init(){

		$this->action = '/' . self::$ajaxModuleName . '/acl-groups/save/';
		$this->actionName = 'acl-groups';

		$this->fields = array(
	 					  'name' => new ArOn_Crud_Form_Field_Text('name','Name:'),                         
			
	 					  'rules' => new ArOn_Crud_Form_Field_Many2Many('rules','Rules:')	 					 
		 
		);
		$this->fields['rules']->helper = array(
     										'model' => 'Db_AclRules',
     										'workingModel' => 'Db_AclGroupRules',
		);
			


		 
		$this->groups = array(
         				'Form' => array('name','rules')
		);
		 
		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');
		 
		parent::init();
	}
}
