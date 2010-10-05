<?php
class Crud_Form_ExtJs_Acl_Resources extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_AclResources';
	protected $_title = 'Ресурсы';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/acl-resources/save/';
		$this->actionName = 'acl-resources';

		$this->fields = array(
        	'id' => new ArOn_Crud_Form_Field_Numeric('acl_resource_id', 'Id', null, true) ,		
        	'acl_module' => new ArOn_Crud_Form_Field_Many2One('acl_module_id','Модуль'),
        	'acl_name' => new ArOn_Crud_Form_Field_Text('acl_resource_name', 'Название', null, true) 
		);
		$this->fields['acl_module']->model = 'Db_AclModules';
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
