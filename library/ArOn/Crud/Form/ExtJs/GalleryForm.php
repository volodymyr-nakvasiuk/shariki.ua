<?php
class ArOn_Crud_Form_ExtJs_GalleryForm extends ArOn_Crud_Form {

	protected $_CRUD_NAME = 'crud';
	protected $_ExjDestopName = 'MyDesktop';
	protected $_title = 'Загрузка пакета фото';
	protected $_main_grid = false;
	protected $_item_var = 'multiUploader';
	protected $_action_index = 'center';
	protected $_region = 'center';
	protected $_permitted_extensions = array("'jpg'", "'gif'", "'jpeg'", "'png'", "'bmp'");
	protected $_reset_on_hide = 'false';
	protected $_allow_close_on_upload = 'false';
	protected $_upload_autostart = 'false';
	protected $_parent_grid_id = "tabs-win-";
	protected $_post_var_name = 'file';	

	public function init() {
		parent::init();
	}

	private $_formTemplatePrefix = 'ArOn_Crud_Form_ExtJs_';

	public function setRegion($region){
		$this->_region = $region;
		return $this;
	}

	public function render(){

		$form_id = "multiuploader-win-".$this->actionName;
		if ($this->_main_grid){
			$grid_id = "grid-win-".$this->_main_grid;
		}
		else {
			$grid_id = "grid-win-".$this->actionName;
		}

		$html = "
		<script type=\"text/javascript\">
			" . $this->_CRUD_NAME . ".mymultiuploader = function() {
		";

		$params = $this->getData();
		$param_arr = array();
		foreach($params as $key => $val){
			if (empty($val) && $val !== 0 && $val !== '0') continue;
			$param_arr[] = '\''.$key.'\':\''.$val.'\'';
		}
		$baseparams = '{'.implode(',',$param_arr).'}';
			
		$html .= "
					return {
						init: function() {
							var desktop = " . $this->_ExjDestopName . ".getModule('" . $grid_id . "').app.getDesktop();
							var win = desktop.getWindow('" . $form_id . "');
							if(!win){
								win = desktop.createWindow({
									parent_grid_id: '" . $this->_parent_grid_id . "',
									id:'" . $form_id . "',
									title:'" . $this->_title . "',
									iconCls:'bogus',
									animCollapse:false,
									constrainHeader:true,
				    				shim:false,
				    				post_var_name: '".$this->_post_var_name."',
	    							url:'/" . self::$ajaxModuleName . "/form/" . $this->actionName . "/save',
	    							".(
		($this->_permitted_extensions)?
										"permitted_extensions: [".implode(', ', $this->_permitted_extensions)."],":
										""
										)."
									reset_on_hide: ".$this->_reset_on_hide.",
									allow_close_on_upload: ".$this->_allow_close_on_upload.",
									upload_autostart: ".$this->_upload_autostart.",
									base_params: ".$baseparams."
								}, Ext.ux.UploadDialog.Dialog);
								win.on('uploadcomplete', function (){
									var desktop = " . $this->_ExjDestopName . ".getModule('" . $grid_id . "').app.getDesktop();
                    				var items = desktop.getWindow('" . $this->_parent_grid_id . "').items;
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
									//var win = desktop.getWindow('" . $form_id . "');
									//win.close();
								}); 
							}
							Ext.get('loading').hide();
							var bp = win.getBaseParams();
							bp['parent_id'] = '".$params['parent_id']."';
							win.setBaseParams(bp);
							win.show();
						}
					};
				}();
				
				Ext.onReady(" . $this->_CRUD_NAME. ".mymultiuploader.init, " . $this->_CRUD_NAME. ".mymultiuploader);
				
				</script>";
										return $html;
	}

	public function saveData($params) {
		$result = parent::saveData($params);
		return $result;
	}

	public function saveValidData() {
		$result = parent::saveValidData();
		return $result;
	}

	public function getItem(){
		return $this->_item_var.'_'.$this->_action_index;
	}
}