<?php
class Crud_Form_ExtJs_AclRules extends ArOn_Crud_Form_ExtJs {
	protected $modelName = 'Db_AclRules';
		
	public function init(){

		$this->action = '/' . self::$ajaxModuleName . '/acl-rules/save/';
		$this->actionName = 'acl-rules';
			
		$this->fields = array(
	 					  'name' => new ArOn_Crud_Form_Field_Text('name','Name:'),                         
	 					  'description' => new ArOn_Crud_Form_Field_TextArea('description','Description:'),
	 					  'perm' => new ArOn_Crud_Form_Field_Array2Select('perm','Permission:'),
	 					  'type' => new ArOn_Crud_Form_Field_Array2Select('type','Type:'),
	 					  'param' => new ArOn_Crud_Form_Field_Text('param','Param:'),	 					 
		 
		);


		$this->fields['perm']->helper = array('options' => Zend_Registry::get('perm') );
		$this->fields['type']->helper = array('options' => Zend_Registry::get('type') );
		 
		$this->groups = array(
         				'Form' => array('name','description','perm','type','param')
		);
		 
		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');

		parent::init();
	}
}
