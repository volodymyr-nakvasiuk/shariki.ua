<?php
class Crud_Form_ExtJs_SiteActs extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_SiteActs';
	protected $_title = 'Экшины сайта';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/site-acts/save/';
		$this->actionName = 'site-acts';

		$this->fields = array(
        	'id' => new ArOn_Crud_Form_Field_Numeric('action_id', 'Id', null, true) ,
        	'sitemodule' => new ArOn_Crud_Form_Field_Many2One('module','Модуль'),
			'sitecontroller' => new ArOn_Crud_Form_Field_Array2Select('action_controller_id','Контроллер'),
        	'name' => new ArOn_Crud_Form_Field_Text('action_name', 'Alt имя', null, true) ,        	
			'status' => new ArOn_Crud_Form_Field_Array2Select('action_status', 'Статус', null, true)	
		);	
		
		$this->fields['sitecontroller']->addAttrib('actionUrl', 'site-controller');
	
		$this->fields['status']->setOptions(array('active' => "Активный", 'not_active' => "Не активный"));		
		$this->fields['sitemodule']->model = 'Db_SiteModule';
		
		$this->fields['sitecontroller']->addAttrib('actionId', $this->actionId);
		$this->fields['sitemodule']->onchange = "function( combo, record, index ) {
        														var form = ".$this->getItem().".getForm();
        														var value = record.get('optionValue');
        														var controller = form.items.get('".$this->actionName."-sitecontroller-id-".$this->actionId."');
        														var store = controller.getStore();
        														store.baseParams = store.baseParams || {};
            													store.baseParams['parent_id'] = value;
        														store.load();
        														controller.clearValue(); 
    															}";

		/*$this->fields['model']->helper = array(
		 'model' => 'Db_controller',
		 'category' => array('Db_Emark')
		 );*/
		 
		if(!empty($this->actionId)){
			$model = Db_SiteController::getInstance();
			$controller = $model->fetchRow($model->getPrimary()." = ".$this->_data ['action_controller_id']);
			$module_id = $controller [ 'controller_module_id' ];
			
			$this->fields['sitecontroller']->addAttrib('parent_id', $module_id);
			$this->fields['sitecontroller']->setValue($this->_data ['action_controller_id'] );
			$this->fields['sitemodule']->setValue($module_id);
		}
		$this->fields['sitecontroller']->setOptions(array());

		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');
		 
		//$this->fields['start']->setOptions(Zend_Registry::get('year'));
		//$this->fields['end']->setOptions(Zend_Registry::get('year'));
		$this->fields['sitecontroller']->setElementHelper('formSelectAutoLoad');
		
		$this->groups = array('0' => array_keys($this->fields));
		parent::init();


	}
}
