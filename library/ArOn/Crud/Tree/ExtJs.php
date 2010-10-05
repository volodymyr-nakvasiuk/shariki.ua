<?php
class ArOn_Crud_Tree_ExtJs extends ArOn_Crud_Tree {
	
	protected $_parent_store = 'store_center';

	protected $_CRUD_NAME = 'crud';
	protected $_itemName = false;
	protected $_ExjDestopName = 'MyDesktop';	
	
	protected $_region = 'center';
	protected $_split = true;
	protected $_useArrows = true;
	protected $_autoScroll = true;
	protected $_animate = true;
	protected $_containerScroll = true;
	protected $_border = false;
	protected $_width = 300;
	protected $_expand = true;
	
	protected $_item_var  = 'tree';	
	protected $_action_index = 'center';
	protected $_is_associated = false;
	
	protected $_module_id;
	protected $_win_id;
	
	protected $_rootNode = array(
		'text' => 'ExtJS Tree',
		'draggable' => false,
		'id' => 'root'
	);
	protected $windowTitle = 'Tree';
	
	public function setParentStore($store_name){
		$this->_parent_store = $store_name;
		return $this;
	}
	
	public function preRender() {
		$this->_win_id = "tree-win-" . $this->ajaxActionName;
		if ($this->_is_associated){
			$this->_module_id = "grid-win-" . $this->ajaxActionName;
		}
		else {
			$this->_module_id = $this->_win_id;
		}
		
	}
	
	public function render() {
		$this->preRender();
		
		$html = '';
		
		if ($this->_is_associated){
			$html .= $this->renderBody();
			$html .= $this->renderAssociation();
		}
		else {
			$html = '<script type="text/javascript">' . "\r\n";
			$html .= $this->_CRUD_NAME . ".mytree = function() {";
			
			$html .= $this->renderBody();
			
			if (!$this->_is_associated) $html .= $this->renderWin();
			
			$html .= "}();";
			$html .= "Ext.onReady(" . $this->_CRUD_NAME . ".mytree.init, " . $this->_CRUD_NAME . ".mytree);";
			$html .= "\r\n" . '</script>';
		}
		
		return $html;
	}
	
	public function getItem(){
		if (!$this->_itemName) $this->setItem();
		return $this->_itemName;
	}
	
	protected function setItem(){
		$this->_itemName = str_replace('-', '__', $this->_item_var.'_'.$this->ajaxActionName.'_'.$this->_action_index);
	}
	
	public function renderBody() {
		$html = '';
		
		$html .= "var ".$this->getItem()." = new Ext.tree.TreePanel({
	    	region: '".$this->_region."',
	    	split: ".($this->_split?'true':'false').",
	        useArrows: ".($this->_useArrows?'true':'false').",
	        autoScroll: ".($this->_autoScroll?'true':'false').",
	        animate: ".($this->_animate?'true':'false').",
	        containerScroll: ".($this->_containerScroll?'true':'false').",
	        border: ".($this->_border?'true':'false').",
	        width: ".$this->_width.",
	        dataUrl: '/" . self::$ajaxModuleName . "/tree/" . $this->ajaxActionName . "/node',
	        root: {
	            nodeType: 'async',
	            text: '".$this->_rootNode['text']."',
	            draggable: ".($this->_rootNode['draggable']?'true':'false').",
	            id: '".$this->_rootNode['id']."'
	        },
	        tbar:[{
	    		text:'Обновить',
	    		tooltip:'Обновить дерево каталога',
	    		iconCls:'refresh',
	    		handler: function (){
	    			".$this->getItem().".getLoader().load(".$this->getItem().".getRootNode(), function(){
	    				".$this->getItem().".getRootNode().expand();
	        		});
	    		}
	    	}]
	    });";
		
		$html .= $this->renderActions();
		
		if ($this->_expand) $html .= $this->getItem().".getRootNode().expand();";
		
		return $html;
	}
	
	protected function renderActions() {
		$html = ''; //must be foreach of ACTIONs array render ACTION
		return $html;
	}
	
	protected function renderAssociation() {
		$html = '';
		
		$html .= $this->getItem().".on('click', function (node, obj){
            ".$this->clickAction()."
	    });";
		
		return $html;
	}
	
	protected function clickAction() {
		$html = '';
		return $html;
	}
	
	public function renderWin() {
		$html = '';
		
		$html .= "
					return {
						init: function() {
							var desktop = " . $this->_ExjDestopName . ".getModule('" . $this->_module_id . "').app.getDesktop();
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
									items: " . $this->getItem() .  ",
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
	
	public function setRegion($region){
		$this->_region = $region;
		return $this;
	}
	
	public function setActionIndex($action_index){
		$this->_action_index = $action_index;
		return $this;
	}
	
	public function makeAssociated(){
		$this->_is_associated = true;
		return $this;
	}
	
	public function getGridWidth(){
		return $this->_width;
	}
	
}
