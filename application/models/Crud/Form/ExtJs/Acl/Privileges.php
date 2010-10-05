<?php
class Crud_Form_ExtJs_Acl_Privileges extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_AclPrivileges';
	protected $_title = 'Привилегии';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/acl-privileges/save/';
		$this->actionName = 'acl-privileges';

		$this->fields = array(
        	'id' => new ArOn_Crud_Form_Field_Numeric('acl_privilege_id', 'Id', null, true) ,			
			'acl_module' => new ArOn_Crud_Form_Field_Many2One('acl_module_id','Модуль'),
        	'acl_resources' => new ArOn_Crud_Form_Field_Array2Select('acl_resource_id','Ресурс'),
			'name' => new ArOn_Crud_Form_Field_Text('acl_privilege_name', 'Название', null, true),
			//'acl-resources' => new ArOn_Crud_Form_Field_Many2One('acl_resource_id','Ресурс'),        	
			'roles' => new ArOn_Crud_Form_Field_Many2Many('roles','Роли') 
		);
		
		$this->fields['acl_module']->model = 'Db_AclModules';		
		$this->fields['acl_module']->onchange = "function( combo, record, index ) {
        														var form = ".$this->getItem().".getForm();
        														var value = record.get('optionValue');
        														var resource = form.items.get('".$this->actionName."-acl_resources-id-".$this->actionId."');
        														var store = resource.getStore();
        														store.baseParams = store.baseParams || {};
            													store.baseParams['parent_id'] = value;
        														store.load();
        														resource.clearValue(); 
    															}";
		
		//$this->fields['acl_resources']->model = 'Db_AclResources';
		$this->fields['acl_resources']->setOptions(array());
		$this->fields['acl_resources']->setElementHelper('formSelectAutoLoad');
		$this->fields['acl_resources']->addAttrib('actionId', $this->actionId);
		
		if(!empty($this->actionId) || ($module = $this->getFilterParam('acl_module')) ){
			if(!empty($this->actionId)){
				$model = Db_AclResources::getInstance();
				$model = $model->fetchRow($model->getPrimary()." = ".$this->_data ['acl_resource_id']);
				$module = $model [ 'acl_module_id' ];
			}
			$this->fields['acl_resources']->addAttrib('parent_id',$module);
			$this->fields['acl_resources']->setValue($model [ 'acl_resource_name' ] );
			$this->fields['acl_module']->setValue($module);
		}
		
		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');
		$this->fields['roles']->helper = array(
                                             'model' => 'Db_AclRoles',                                             
                                             'workingModel' => 'Db_AclRolePrivileges'
		);
		//$this->fields['roles']->optionName = "CONCAT_WS(', ',model_name,concat(price_value,'$'),price_year)";
		$this->fields['roles']->setElementHelper('formMultiSelect');
		$this->fields['roles']->setExplode(',');
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
