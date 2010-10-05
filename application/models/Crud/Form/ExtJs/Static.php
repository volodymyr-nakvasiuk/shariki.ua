<?php
class Crud_Form_ExtJs_Static extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_Static';
	protected $_title = 'Статическая страница сайта';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/static/save/';
		$this->actionName = 'static';

		$this->fields = array(
			'id' => new ArOn_Crud_Form_Field_Numeric('static_id', 'Id', null, true) ,
			'sitemodule' => new ArOn_Crud_Form_Field_Many2One('static_module_id','Модуль', null, true),
			'sitecontroller' => new ArOn_Crud_Form_Field_Array2Select('static_controller_id','Контроллер', null, true),
			'siteaction' => new ArOn_Crud_Form_Field_Array2Select('static_action','Экшн', null, true),
			'title' => new ArOn_Crud_Form_Field_Text('static_title', 'Заголовок', null, true) ,
			'text' => new ArOn_Crud_Form_Field_TextArea('static_text', 'Текст на странице') ,
		);

		$this->fields['sitecontroller']->addAttrib('actionUrl', 'site-controller');
		$this->fields['siteaction']->addAttrib('actionUrl', 'site-acts');
		
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
		$this->fields['siteaction']->addAttrib('actionId', $this->actionId);
		$this->fields['sitecontroller']->onchange = "function( combo, record, index ) {
        														var form = ".$this->getItem().".getForm();
        														var value = record.get('optionValue');
        														var action = form.items.get('".$this->actionName."-siteaction-id-".$this->actionId."');
        														var store = action.getStore();
        														store.baseParams = store.baseParams || {};
            													store.baseParams['parent_id'] = value;
        														store.load();
        														action.clearValue(); 
    															}";

		/*$this->fields['model']->helper = array(
		 'model' => 'Db_controller',
		 'category' => array('Db_Emark')
		 );*/
		 
		if(!empty($this->actionId)){
			$model = Db_SiteActs::getInstance();
			$action = $model->fetchRow($model->getPrimary()." = ".$this->_data ['static_action']);
			$controller_id = $action [ 'action_controller_id' ];
			
			$model = Db_SiteController::getInstance();
			$controller = $model->fetchRow($model->getPrimary()." = ".$controller_id);
			$module_id = $controller [ 'controller_module_id' ];
			
			$this->fields['siteaction']->addAttrib('parent_id', $controller_id);
			$this->fields['siteaction']->setValue($this->_data ['static_action'] );
			$this->fields['sitecontroller']->addAttrib('parent_id', $module_id);
			$this->fields['sitecontroller']->setValue($controller_id);
			$this->fields['sitemodule']->setValue($module_id);
		}
		$this->fields['siteaction']->setOptions(array());
		$this->fields['sitecontroller']->setOptions(array());

		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');
		 
		//$this->fields['start']->setOptions(Zend_Registry::get('year'));
		//$this->fields['end']->setOptions(Zend_Registry::get('year'));
		$this->fields['siteaction']->setElementHelper('formSelectAutoLoad');
		$this->fields['sitecontroller']->setElementHelper('formSelectAutoLoad');
		
		$this->groups = array('0' => array_keys($this->fields));
		parent::init();

	}
}
