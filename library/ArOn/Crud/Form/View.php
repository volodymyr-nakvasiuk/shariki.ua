<?php
class ArOn_Crud_Form_View extends ArOn_Crud_Form_ExtJs {

	protected $viewTemplate;		
	
	/**
	 * 
	 * @var Zend_View
	 */
	protected $_view;
	
	protected $_body;
	
	
	public function init() {
		parent::init();		
	}

	public function render(){
		
		if(empty($this->windowTitle)){
			$this->windowTitle = $this->_title;
			$this->_title = '';
		}

		$html = "
		<script type=\"text/javascript\">
			" . $this->_CRUD_NAME . ".myform = function() {
		";
		$this->renderCore();
		$html .= "
					return {
						init: function() {
							var desktop = " . $this->_ExjDestopName . ".getModule('" . $this->_grid_id . "').app.getDesktop();
							var win = desktop.getWindow('" . $this->_form_id . "');
							if(!win){
								win = desktop.createWindow({
									parent_grid_id: '" . $this->_parent_grid_id . "',
									id:'" . $this->_form_id . "',
									title:'" . $this->windowTitle . "',
									width:" . $this->_width .  ",
									height:550,
									minWidth:300,
									minHeight:200,
									iconCls:'bogus',
									animCollapse:false,
									constrainHeader:true,
				    				shim:false,
				    				html: '" . $this->_body	. "',
				    				".$this->renderButtons().",
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
		$html = '';

		$extjsFormItems = array();
		$elements = $this->getElements();
		$data = array();
		if(!empty($elements))
		foreach ($elements as $element){
			$name = substr($element->helper,4);
			if($name == 'Submit') continue;
			$key = $element->getName();
			$value = $element->getValue();
			if($element->getAttrib('width') > $this->_width) $this->_width = $element->getAttrib('width');
			$data[$key] = $value;
		}
				
		$this->_body = $this->renderTemplate($data);
		if($this->_width > 0) $this->_width += 230;
	}
	
	protected function renderButtons(){
		$html = '';
		$html .=  "
					    buttons: [{
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
	
	public function saveData() {		
		return false;
	}

	public function saveValidData() {		
		return false;
	}
	
	public function setTemplate($template){
		$this->viewTemplate = $template;
	}
	
	public function renderTemplate(array $data){
		$view = new Zend_View ( array ('encoding' => 'UTF-8' ) );
		$view->addScriptPath(CRUD_PATH.'/Form/View/Template/');
		$this->setView($view);
		foreach ($data as $key => $value){
			$value = $this->fields[$key]->getRenderValue();
			$this->_view->$key = $value;
		}
		$html =	$this->_view->render($this->viewTemplate);
		$html = trim($html);
		$html = addcslashes($html,"\\\'\"&\n\r<>");
		return $html;
		//return 'test';
	}
	
}