<?php
class ArOn_Crud_Window_ExtJs extends ArOn_Crud_Window {
	
	protected $_CRUD_NAME = 'crud';
	protected $_itemName = false;
	protected $_ExjDestopName = 'MyDesktop';

	protected $_items = array();
	protected $_width = 550;
	protected $_win_id;
	protected $windowTitle = 'Window';

	public function setup() {
		parent::setup();
		foreach ($this->_items as $key=>&$item){
			$element = 'ArOn_Crud_Window_ExtJs_'.($item['element']?ucfirst($item['element']):'Element');
			if (class_exists($element)) $item = new $element($item);
			else unset($this->_items[$key]);
		}
	}
	
	public function preRender() {
		$this->_win_id = "sample-win-" . $this->ajaxActionName;
	}
	
	public function render() {
		$this->preRender();
		
		$html = '<script type="text/javascript">' . "\r\n";
		$html .= $this->_CRUD_NAME . ".mywindow = function() {";

		$html .= $this->renderBody();

		$html .= $this->renderWin();

		$html .= "}();";
		$html .= "Ext.onReady(" . $this->_CRUD_NAME . ".mywindow.init, " . $this->_CRUD_NAME . ".mywindow);";
		$html .= "\r\n" . '</script>';

		return $html;
	}
	
	public function getItems(){
		$items = array();
		foreach ($this->_items as $item){
			$items[] = $item->getItem();
		}
		return '['.implode(',', $items).']';
	}

	public function renderBody() {
		$html = '';

		foreach ($this->_items as $item){
			$html .= "var ".$item->getItem()." = ".$item->render();
		}

		return $html;
	}
	

	public function renderWin() {
		$html = '';
		
		$html .= "
					return {
						init: function() {
							var desktop = " . $this->_ExjDestopName . ".getModule('" . $this->_win_id . "').app.getDesktop();
							var win = desktop.getWindow('" . $this->_win_id . "');
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
									id:'" . $this->_win_id . "',
									title:'" . $this->windowTitle . "',
									width:" . $this->_width .  ",
									height:win_height,
									minWidth:300,
									minHeight:200,
									iconCls:'bogus',
									animCollapse:false,
									constrainHeader:true,
				    				shim:false,
									items: " . $this->getItems() .  ",
									forceLayout: true,
									layout:'fit'
								});
							}
							Ext.get('loading').hide();
							win.show();
						}
					};";
		
		return $html;
	}
}
