<?php
class ArOn_Crud_Grid_ExtJs_TabGrid extends ArOn_Crud_Grid {

	protected $_CRUD_NAME = 'crud';
	protected $_tabs   = array(); //массив связанных с данным гридом елементов
	protected $_region = 'center';
	protected $_width = 0;
	protected $_win_id;

	public function render() {
		$this->preRender ();
		$html = $this->renderTitle ();
		return $html;
	}

	public function preRender() {
		$this->_win_id = "tabs-win-" . $this->ajaxActionName;
		$params = $this->filters->getDefaultValues();
		$this->setFilterParams($this->_params);
	}

	public function setRegion($region){
		$this->_region = $region;
		return $this;
	}

	public function getGridWidth(){
		return $this->_width;
	}

	public function getItem(){
		return $this->_item_var.'_'.$this->_action_index;
	}

	protected function renderTitle() {

		$html = '<script type="text/javascript">' . "\r\n";

		$html .= $this->_CRUD_NAME . ".mytabpanel = function() {";

		$items = array();
		foreach ($this->_tabs as $item){
			if($item instanceof ArOn_Crud_Form_ExtJs){
				$item->createForm();
				$item->setFormId($this->_win_id);
				$item->setGridId("grid-win-".$this->ajaxActionName);
			}
			$item_html = $item->renderCore();
			$html .= $item_html;
			$items[] = $item->getItem();
			if($item->getGridWidth() > $this->_width) $this->_width = $item->getGridWidth();
		}
		if($this->_width > 0) $this->_width += 50;
		
		$html .= "
    	return {
	    	init: function() {
	    		var desktop = MyDesktop.getModule('grid-win-" . $this->ajaxActionName . "').app.getDesktop();
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
				if (win_height<200){
					win_height = 200;
				}
	    		if(!win){
	    			win = desktop.createWindow({
	    				id:'" . $this->_win_id . "',
	    				title:'" . $this->gridTitle . "',
	    				height:win_height,
	    				width:" . $this->_width . ",
	    				iconCls:'tabs',
	    				shim:false,
	    				animCollapse:false,
	    				constrainHeader:true,
	    				layout:'border',
	    				forceLayout: true,
	    				items: [
	    					new Ext.TabPanel({
	    						listeners:{
									render: function(obj){
										for (var i=0;i<".count($items).";i++) obj.setActiveTab(i);
										obj.setActiveTab(0);
									}
								},
	    						activeTab: 0,
	    						region: '".$this->_region."',
        						items: [".implode(', ', $items)."]
							})
						]".$this->renderButtons()."
	    			});
	    		}
	    		Ext.get('loading').hide();
	    		win.show();
			}
		};
	}();
    
		Ext.onReady(" . $this->_CRUD_NAME . ".mytabpanel.init, " . $this->_CRUD_NAME . ".mytabpanel);";
		 
		$html .= "\r\n" . '</script>';

		return $html;

	}

	protected function renderButtons(){
		$html = "";
		return $html;
	}
	
	public function getWinId(){
		return $this->_win_id;
	}
	
	public function setWinId($win_id){
		$this->_win_id = $win_id;
		return $this;
	}
}
