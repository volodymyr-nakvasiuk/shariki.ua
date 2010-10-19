<?php
class ArOn_Crud_Form_ExtJs extends ArOn_Crud_Form {

	protected $_CRUD_NAME = 'crud';
	protected $_ExjDestopName = 'MyDesktop';
	protected $_title;
	protected $_main_grid = false;
	protected $_item_var = 'formPanel';
	protected $_action_index = 'center';
	protected $_region = 'center';
	protected $_form_id;
	protected $_grid_id;
	protected $_parent_grid_id = false;
	protected $windowTitle;
	protected $_width = 0;
	protected $_height = 0;
	protected $_is_associated = false;

	public function setTitle($title) {
		$this->_title = $title;
		return $this;
	}

	public function setItemVar($name, $append=false) {
		$this->_item_var = $append?$this->_item_var.$name:$name;
		return $this;
	}

	public function init() {
		parent::init();
	}
	
	public function makeAssociated(){
		$this->_is_associated = true;
		return $this;
	}
	
	public function createForm(){
		parent::createForm();
		
		$this->_form_id = "form-win-".$this->actionName;
		if ($this->_main_grid){
			$this->_grid_id = "grid-win-".$this->_main_grid;
		}
		else {
			$this->_grid_id = "grid-win-".$this->actionName;
		}
		if (!$this->_parent_grid_id){
			$this->_parent_grid_id = "grid-win-".$this->actionName;
		}
		
		if(!empty($this->actionId)) $this->_form_id .= "-".$this->actionId;
	}

	private $_formTemplatePrefix = 'ArOn_Crud_Form_ExtJs_';

	public function setRegion($region){
		$this->_region = $region;
		return $this;
	}
	
	public function setActionIndex($action_index){
		$this->_action_index = $action_index;
		return $this;
	}
	
	public function render(){
		
		if(empty($this->windowTitle)){
			$this->windowTitle = $this->_title;
			$this->_title = '';
		}

		$html = "<script type=\"text/javascript\">
			" . $this->_CRUD_NAME . ".myform = function() {
		";
		$html .= $this->renderCore();
		$html .= "
					return {
						init: function() {
							var desktop = " . $this->_ExjDestopName . ".getModule('" . $this->_grid_id . "').app.getDesktop();
							var win = desktop.getWindow('" . $this->_form_id . "');
							var win_height = 430;
							var client_height = 480;
							if (parseInt(navigator.appVersion)>3) {
			 					if (navigator.appName==\"Netscape\") {
									client_height = window.innerHeight;
								}
								if (navigator.appName.indexOf(\"Microsoft\")!=-1) {
									client_height = document.body.offsetHeight;
								}
							}
							client_height = client_height - 80;
							if (client_height < win_height){
								win_height = client_height;
							}
							if(!win){
								win = desktop.createWindow({
									parent_grid_id: '" . $this->_parent_grid_id . "',
									id:'" . $this->_form_id . "',
									title:'" . $this->windowTitle . "',
									width:" . $this->_width .  ",
									height:win_height,
									minWidth:300,
									minHeight:200,
									iconCls:'bogus',
									animCollapse:false,
									constrainHeader:true,
									shim:false,
									items: " . $this->getItem() .  ",
									forceLayout: true,
									layout:'fit'
								});
							}
							Ext.get('loading').hide();
							win.show();
						}
					};
				}();
				
				Ext.onReady(" . $this->_CRUD_NAME. ".myform.init, " . $this->_CRUD_NAME. ".myform);
				
				</script>";
		return $html;
	}

	public function renderCore (){
		$phpData = array(
			'actionName'=>$this->actionName,
			'_ExjDestopName'=>$this->_ExjDestopName,
			'_grid_id'=>$this->_grid_id,
			'_parent_grid_id'=>($this->_parent_grid_id?$this->_parent_grid_id:$this->_grid_id),
			'_form_id'=>$this->_form_id,
		);
		$html = "var " . $this->getItem() . " = new Ext.FormPanel({
					phpData: ".Zend_Json_Encoder::encode($phpData).",
					title:'" . $this->_title . "',
					region: '".$this->_region."',
					labelWidth: 150,
					id: 'form-".$this->_item_var."-".$this->actionId."',
					name: 'form-".$this->_item_var."-".$this->actionId."',
					frame: true,
					autoScroll: true,
					bodyStyle:'padding:5px 5px 0',
					border:false,
				";

		if ($this->getAttrib ( 'enctype') == 'multipart/form-data'){
			$html .= "fileUpload: true, // If you want to upload files
				";
		}

		$extjsFormItems = array();
		$elements = $this->getElements();
		if(!empty($elements))
		foreach ($elements as $element){
			$name = substr($element->helper,4);
			if($name == 'Submit') continue;
			$classname = (class_exists($this->_formTemplatePrefix . $name) ) ? $this->_formTemplatePrefix . $name : $this->_formTemplatePrefix . 'Element';
			$extjsFormItem = new $classname ( $element );
			if($element->getAttrib('width') > $this->_width) $this->_width = $element->getAttrib('width');
			$extjsFormItems[] = $extjsFormItem->render();
		}
		$html .= "items: [ " . implode(", ",$extjsFormItems) . "]";

		if($this->_width > 0) $this->_width += 230;
		
		$html .= $this->renderButtons();
		$html .= "});";
		
		return $html;
	}
	
	protected function renderButtons(){
		$html = ",";
		$html .= "
						buttons: [{
							text: 'Сохранить',
							handler: function(){
								var form = " . $this->getItem() . ".getForm();
								if(form.isValid()){
									form.submit({
										url:'/" . self::$ajaxModuleName . "/form/" . $this->actionName . "/save',
										waitMsg: 'Загрузка...',
										waitTitle: 'Пожалуйста подождите...',
										failure: function (form, action){
											if (action.result.message)
												Ext.MessageBox.alert('Ошибка', action.result.message);
											else
												Ext.MessageBox.alert('Ошибка', 'На сервере произошла ошибка, либо сервер недоступен');
										},
										success: function (){
											Ext.MessageBox.hide();
											var desktop = " . $this->_ExjDestopName . ".getModule('" . $this->_grid_id . "').app.getDesktop();
											var win = desktop.getWindow('" . ($this->_parent_grid_id?$this->_parent_grid_id:$this->_grid_id) . "');
											if (win){
												var items = win.items;
												var i;
												for (i=0;i<items.getCount();i++){
													var xtype = items.get(i).getXType();
													if (xtype == 'editorgrid' || xtype == 'grid'){
														items.get(i).getStore().reload();
													}
													if (xtype = 'tabpanel'){
														var tabs = items.get(i).items;
														var j;
														for (j=0;j<tabs.getCount();j++){
															var tabxtype = tabs.get(j).getXType();
															if (tabxtype == 'editorgrid' || tabxtype == 'grid'){
																tabs.get(j).getStore().reload();
															}
														}
													}
												}
											}
											var win = desktop.getWindow('" . $this->_form_id . "');
											win.close();
										}
									});
								}
							}
						},{
							text: 'Закрыть',
							handler: function(){
								var desktop = " . $this->_ExjDestopName . ".getModule('" . $this->_grid_id . "').app.getDesktop();
								var win = desktop.getWindow('" . $this->_form_id . "');
								win.close();
								
							}
						}]
						";
		return $html;
	}
	
	public function getItem(){
		return str_replace('-', '__', $this->_item_var.'_'.$this->actionName.'_'.$this->_action_index);
	}
	
	public function getGridWidth(){
		return $this->_width;
	}
	
	public function getFromId(){
		return $this->_form_id;
	}
	
	public function setFormId($form_id){
		$this->_form_id = $form_id;
		return $this;
	}
	
	public function getGridId(){
		return $this->_grid_id;
	}
	
	public function setGridId($grid_id){
		$this->_grid_id = $grid_id;
		return $this;
	}
}