<?php
class Crud_Form_ExtJs_Acl_Roles extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_AclRoles';
	protected $_title = 'Роли';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/acl-roles/save/';
		$this->actionName = 'acl-roles';

		$this->fields = array(
        	'id' => new ArOn_Crud_Form_Field_Numeric('acl_role_id', 'Id', null, true) ,
        	'name' => new ArOn_Crud_Form_Field_Text('acl_role_name', 'Название', null, true),
			'resources' => new ArOn_Crud_Form_Field_Many2Many('resources','Ресурсы'),
			'acl-privileges' => new ArOn_Crud_Form_Field_Many2Many('privileges','Привилегии')  
		);
				
		$this->fields['resources']->helper = array(
                                             'model' => 'Db_AclResources',                                             
                                             'workingModel' => 'Db_AclPrivileges',
											 'category' => array('Db_AclModules')
		);
		$this->fields['resources']->noTableReference = true;
		$this->fields['resources']->noSave();
		$this->fields['resources']->onchange = "function( field, newValue, oldValue) {
        														var form = ".$this->getItem().".getForm();
        														var value = newValue.split(this.valueSeparator);
        														var privileges = form.items.get('".$this->actionName."-aclprivileges-id-".$this->actionId."');
        														var store = privileges.getStore();
        														store.baseParams = store.baseParams || {};
            													store.baseParams['parent_id[]'] = value;
        														store.load({
        															scope: privileges,
											                        callback: function(){
											                        	this.renderValues();
											                        }
																}); 
        														privileges.clearValue(); 
    															}";
		
		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');
		
		$this->fields['acl-privileges']->addAttrib('actionId', $this->actionId);
		$this->fields['acl-privileges']->helper = array(
                                             'model' => 'Db_AclPrivileges',                                             
                                             'workingModel' => 'Db_AclRolePrivileges',
											 'category' => array('Db_AclResources',array('Db_AclResources','Db_AclModules'))
		);
		$this->fields['acl-privileges']->optionName = "CONCAT_WS(', ',acl_module_name,acl_resource_name,acl_privilege_name)";
		$this->fields['acl-privileges']->setElementHelper('formMultiSelectAutoLoad');
		$this->fields['acl-privileges']->setExplode(',');
		$this->fields['acl-privileges']->setElementType('select');
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
