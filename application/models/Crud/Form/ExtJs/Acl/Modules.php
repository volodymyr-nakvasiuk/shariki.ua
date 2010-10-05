<?php
class Crud_Form_ExtJs_Acl_Modules extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_AclModules';
	protected $_title = 'Модуль';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/acl-modules/save/';
		$this->actionName = 'acl-modules';

		$this->fields = array(
        	'id' => new ArOn_Crud_Form_Field_Numeric('acl_module_id', 'Id', null, true) ,
        	'name' => new ArOn_Crud_Form_Field_Text('acl_module_name', 'Название', null, true) 
		);
		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');
		/*if(!empty($this->actionId)){
		 $this->fields['affiliate_id']->notEdit = true;
		 $this->_alternative_data['update_date'] = date('Y-m-d');
		 }else{
		 $this->fields['status']->checked = true;
		 $this->_alternative_data['create_date'] = date('Y-m-d');
		 $this->_alternative_data['update_date'] = date('Y-m-d');
		 }*/
		//$this->fields['affiliate_id']->nullElement = array('0' => 'All');
		$this->groups = array('0' => array_keys($this->fields));
		parent::init();


	}
}
