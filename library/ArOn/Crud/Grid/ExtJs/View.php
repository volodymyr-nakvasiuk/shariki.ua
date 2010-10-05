<?php
class ArOn_Crud_Grid_ExtJs_View {
	
	protected $_CRUD_NAME = 'crud';
	protected $_ExjDestopName = 'MyDesktop';
	protected $_win_id = false;
	protected $viewTemplate;		
	protected $_view;
	protected $_body;
	protected $_title = '';
	protected $windowTitle = '';
	protected $_width = 300;
	protected $_height = 200;
	protected $_data = array();
	protected $ajaxActionName = 'view';
	protected $_parentModule = false;
	
	protected $_minimizable = 'true';
	protected $_maximizable = 'true';
	protected $_closable    = 'true';
	protected $_resizable   = 'true';
	protected $_maximized   = 'false';
	protected $_minimized   = 'false';
	
	protected $_params;
	
	public function __construct($options = null, $params = array()) {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
        
        $this->_params = $params;
		
        // Extensions...
        $this->init();
    }
    
    public function setOptions(array $options){
    	if (isset($options['winId'])) {
            $this->_win_id = "grid-win-" . $options['winId'];
            unset($options['winId']);
        }
        if (isset($options['width'])) {
            $this->_width = $options['width'];
            unset($options['width']);
        }
    	if (isset($options['height'])) {
            $this->_height = $options['height'];
            unset($options['height']);
        }
    }
    
 	public function setConfig(Zend_Config $config){
        return $this->setOptions($config->toArray());
    }
	
	public function init() {
	}
	
	public function preRender() {
		if(!$this->_win_id)
			$this->_win_id = "grid-win-" . $this->ajaxActionName;
		if ($this->_parentModule) $this->_parentModule = "grid-win-" . $this->_parentModule;
	}
		
	
	public function render(){
		$this->preRender();
		if(empty($this->windowTitle)){
			$this->windowTitle = $this->_title;
			$this->_title = '';
		}
		$this->_body = $this->renderTemplate($this->_data);

		$html = "
		<script type=\"text/javascript\">
			" . $this->_CRUD_NAME . ".myform = function() {
		";

		$html .= "
					return {
						init: function() {
							var desktop = " . $this->_ExjDestopName . ".getModule('" . ($this->_parentModule?$this->_parentModule:$this->_win_id) . "').app.getDesktop();
							var win = desktop.getWindow('" . $this->_win_id . "');
							if(!win){
								win = desktop.createWindow({
									id:'" . $this->_win_id . "',
									title:'" . $this->windowTitle . "',
									width:" . $this->_width .  ",
									height:" . $this->_height .  ",
									minimizable: " . $this->_minimizable .  ",
					            	maximizable: " . $this->_maximizable .  ",
					            	closable: " . $this->_closable .  ",
					            	maximized: " . $this->_maximized .  ",
					            	minimized: " . $this->_minimized .  ",
					            	resizable: " . $this->_resizable .  ",
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
	
	protected function renderButtons(){
		$html = '';
		$html .=  "
					    buttons: [{
				        	text: 'Закрыть',
				        	handler: function(){
					        	var desktop = " . $this->_ExjDestopName . ".getModule('" . ($this->_parentModule?$this->_parentModule:$this->_win_id) . "').app.getDesktop();
								var win = desktop.getWindow('" . $this->_win_id . "');
								win.close();
								
				        	}
				    	}]
					";
		return $html;
	}
	
	
	public function setTemplate($template){
		$this->viewTemplate = $template;
	}
	
 	public function setView(Zend_View_Interface $view = null) {
        $this->_view = $view;
        return $this;
    }
	
	public function renderTemplate(array $data){
		$view = new Zend_View ( array ('encoding' => 'UTF-8' ) );
		$view->addScriptPath(CRUD_PATH.'/Grid/ExtJs/View/Template/');
		$this->setView($view);
		foreach ($data as $key => $value){
			$this->_view->$key = $value;
		}
		$html =	$this->_view->render($this->viewTemplate);
		$html = trim($html);
		$html = addcslashes($html,"\\\'\"&\n\r<>");
		return $html;
	}
	
}