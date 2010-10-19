<?php
class ArOn_Crud_Grid_ExtJs extends ArOn_Crud_Grid {
	
	protected $_ifCount = true;
	
	protected $_idProperty = 'id'; //параметр, который будет использоваться для редактирования,удаления записей из грида
	protected $_CRUD_NAME = 'crud';	
	protected $_associate_with   = array(); //массив связанных с данным гридом елементов
	
	
	protected $_region = 'center';
	protected $_store_var = 'store';
	protected $_item_var  = 'grid';
	protected $_action_index = 'center';
	
	protected $windowTitle;
	protected $_win_id;
	//protected $_width = 'autoWidth: true';
	
	protected $_window_width = 0;
	protected $desktopName;
	protected $_gridWindowStatus = 'normal';
	protected $_windowBlinking = false;
	protected $_autoLoad = true;
	protected $_renderFilters = true;
	protected $_is_associated = false;
	protected $_directionParameter = 'dir';
	
	protected $_baseParams = array();
	
	//protected $_parentParamName = false;
	
	protected $actions = array (
		'create' => array(
			'active' => true,
			'text'   =>'Добавить',
    		'tooltip'=>'Создать новую запись',
		),
		'edit' => array(
			'active' => true,
			'text'   =>'Редактировать',
    		'tooltip'=>'Редактировать выделенную запись',
		),
		'remove' => array(
			'active' => true,
			'text'   =>'Удалить',
    		'tooltip'=>'Удалить выделенные записи',
		),
		//'duplicate' => array(
		//	'active' => true,
		//	'text'   =>'Клонировать',
		//	'tooltip'=>'Клонировать выделенные записи',
		//),
	);
	
	public function setBaseParams($baseParams){
		if (!is_array($baseParams)) $baseParams = array($baseParams);
		$this->_baseParams = $baseParams;
		return $this;
	}
	
	public function getBaseParams(){
		return $this->_baseParams;
	}
	
	public function setBaseParam($name, $value){
		if (!is_array($this->_baseParams)) $this->_baseParams = array();
		$this->_baseParams[$name] = $value;
		return $this;
	}
	
	public function getBaseParam($name){
		return $this->_baseParams[$name];
	}
	
	public function configActions($actions_config){
		foreach ($actions_config as $action_name=>$action){
			if ($action === false){
				$this->actions[$action_name]['active'] = false;
				continue;
			}
			foreach($action as $param_name=>$param_value){
				$this->actions[$action_name][$param_name] = $param_value;
			}
		}
	}
	
	public function render() {
		if(empty($this->windowTitle)){
			$this->windowTitle = $this->gridTitle;
			$this->gridTitle = '';
		}
		$this->preRender ();
		$html = $this->renderTitle ();
		return $html;
	}
	
	public function makeAssociated(){
		$this->_is_associated = true;
		return $this;
	}
	
	public function preRender() {
		$this->_win_id = "grid-win-" . $this->ajaxActionName;
		$params = $this->filters->getDefaultValues();
		$this->setFilterParams($this->_params);
		if(!$this->_is_associated){
			$this->_width = 20;
			foreach ( $this->fieldNames as $name => $field ) {
				if(!$this->fieldData [$name]->isHidden())
					$this->_width += $this->fieldData [$name]->getWidth();
			}
			$this->_window_width += $this->_width;
			foreach($this->_associate_with as $key => $item){
				if($item instanceof ArOn_Crud_Grid_ExtJs_MultiGrid){
					$item->setRegion($key)
						 ->setActionIndex($key)
						 ->setParentStore($this->_store_var.'_'.$this->_action_index)
						 ->makeAssociated();
				}elseif($item instanceof ArOn_Crud_Form_ExtJs){
					$item->createForm();
					$item->setRegion($key)
						 ->setActionIndex($key)
						 ->makeAssociated();
				}elseif($item instanceof ArOn_Crud_Tree_ExtJs){
					$item->setRegion($key)
						 ->setActionIndex($key)
						 ->setParentStore($this->_store_var.'_'.$this->_action_index)
						 ->makeAssociated();
				}
				$this->_window_width += $item->getGridWidth();
			}
			$this->_width_coef = 580/($this->_width-20);
			if ($this->_width_coef < 1){
				$this->_width_coef = 1;
			}
			else {
				$this->_window_width -= $this->_width;
				$this->_width = 600;
				$this->_window_width += $this->_width;
			}
		}
		else {
			$this->_width_coef = 1;
		}
	}
	
	public function setRegion($region){
		$this->_region = $region;
		return $this;
	}
	
	public function setGridWindowStatus($windowStatus){
		$this->_gridWindowStatus = $windowStatus;
		return $this;
	}
	
	public function setWindowBlinking($windowBlinking){
		$this->_windowBlinking = $windowBlinking;
		return $this;
	}
	
	public function setActionIndex($action_index){
		$this->_action_index = $action_index;
		return $this;
	}
	
	public function renderLinks() {
		$limit = $this->getLimit();
		$limit = ($limit == 'all') ? 0 : $limit;
		$bbar = "";
		if ($this->renderPager){
			$bbar = "bbar: new Ext.PagingToolbar({
        		pageSize: " . $limit . ",
        		store: ".$this->_store_var.'_'.$this->_action_index.",
        		displayInfo:true,
        		displayMsg:'Отображаются записи {0} - {1} из {2}',
        		emptyMsg:'Нет записей для отображения'
       		}),	"; 
		}
		$tbar = array();
		if ($this->renderAction){
			foreach ($this->actions as $action_name=>$action){
				if (!$action['active']) continue;
				$tbar[] = "{
	    			text:'".$action['text']."',
	    			tooltip:'".$action['tooltip']."',
	    			iconCls:'".$action_name."',
	    			handler:".$action_name."_".$this->_action_index."
	    		}"; 
			}
		}
		if ($this->_renderFilters){
			$tbar[] = "' Поиск:',
		    	new Ext.form.TriggerField({
		    		initComponent : function(){
		        		Ext.form.TriggerField.superclass.initComponent.call(this);
		        		this.on('specialkey', function(f, e){
		            		if(e.getKey() == e.ENTER){
		                		this.onTriggerClick();
		            		}
		        		}, this);
		    		},
		    		onTriggerClick : function(){
		    			var v = this.getRawValue();
		            	".$this->_store_var.'_'.$this->_action_index.".baseParams = ".$this->_store_var.'_'.$this->_action_index.".baseParams || {};
		            	".$this->_store_var.'_'.$this->_action_index.".baseParams['search'] = v;
		            	".$this->_store_var.'_'.$this->_action_index.".reload({params: {start:0}});
		    		}
				})";
		}
		$html = "
		".$bbar."
        tbar:[".implode(", '-',", $tbar)."],";
		
		return $html;
		
	}
	
	public function renderGridTable() {

		$pagination = $this->renderFoot ();		
		$html .= $pagination;		
		$html .= $this->renderHead ();		
		$html .= $this->renderBody ();		
		return $html;
	}
	
	public function renderFilters() {
		//$this->filters->createForm();	
		//return $this->filters->fields ? $this->filters->render () : '';
	}
	
	
	public function renderHead() {}
	
	protected function parseBaseParams($params){
		$parram_key_value = array();
		foreach($params as $val){
			$parram_key_value[$val['name']] = $val['value'];
		}
		
		//if ($this->_parentParamName && $parram_key_value['parent'])
		//	$parram_key_value['formFilterParams'] .= '|'.$this->_parentParamName.':'.$parram_key_value['parent'];
		
		$parram_key_value = array_merge($parram_key_value, $this->getBaseParams());
		
		$param_arr = array();
		foreach($parram_key_value as $name=>$value){
			if ($name == 'p' || $name == 'winstatus' || $name == 'winstartblinking') continue;
			$param_arr[] = '\''.$name.'\':\''.$value.'\'';
		}
		return '{'.implode(',',$param_arr).'}';
	}
	
	protected function renderGrid(){
		$params = $this->getAllFilterParams();
		
		$html = "
			var ".$this->_store_var.'_'.$this->_action_index." = new Ext.data.Store({
			url: '/" . self::$ajaxModuleName . "/grid/" . $this->ajaxActionName . "/list',
			remoteSort: true,
			autoLoad: " . (($this->_autoLoad) ? "true" : "false") . ",
			baseParams: ".$this->parseBaseParams($params).",
			reader: new Ext.data.JsonReader({
				root:'rows',
				totalProperty: 'results',
				idProperty:'" . $this->_idProperty . "'
			},
		";
		
		$html .= "[";
		$jsonFields = array();
			
		foreach ( $this->fieldNames as $name => $field ) {
			$fieldTitle = $this->fieldData [$name]->getTitle ();
			if($fieldTitle === false) continue;
			$fieldName = $this->fieldData [$name]->getName ();			
			$fieldType = $this->fieldData [$name]->getType ();
			$jsonFields[] = "{name: '" . $fieldName . "', type: '" . $fieldType . "'}";	
		}
		$html .= implode(',',$jsonFields);
		$html .= "]\r\n";			
		$html .= ")});\r\n";
		//$html .= "".$this->_store_var.'_'.$this->_action_index.".load({params:{}});";
		if ($this->renderAction) $html .= $this->renderAction();
		//$start = ( $this->getPage() ) * $this->getLimit();
		//$html .= "store.load({params:{start:" . $start . ", limit:" . $this->getLimit() . "}});\r\n";
		
		$html .= "var ".$this->getItem()." = new Ext.grid.GridPanel({
						title:'" . $this->gridTitle . "',
        				store: ".$this->_store_var.'_'.$this->_action_index.",";
		$html .= "region: '".$this->_region."',";
		if ($this->_is_associated){
			$html .= "split: true,";
		}
		$html .= " columns: [ \r\n";
		$columns = array();
		foreach ( $this->fieldNames as $name => $field ) {
			$fieldTitle = $this->fieldData [$name]->getTitle ();
			if($fieldTitle === false) continue;
			$fieldName = $this->fieldData [$name]->getName ();
			
			$column = array();
			$column[] = "header: " . '"' . $fieldTitle .'"';
			$column[] = "dataIndex: '" . $fieldName . "'";
			//if($this->fieldData [$name]->rowClass)
				//$column[] = "id: '" . $this->fieldData [$name]->rowClass . "'";
			if($this->fieldData [$name]->isSorted())
				$column[] = "sortable: true";
			if($this->fieldData [$name]->isHidden())
				$column[] = "hidden: true";
			if($render_function = $this->fieldData [$name]->getRenderFunction())
				$column[] = "renderer: " . $render_function;
			if($width = $this->fieldData [$name]->getWidth()){			
				$column[] = "width: " . $width*$this->_width_coef;
			}
			if($child = $this->fieldData [$name]->getAction())			
				$column[] = "childGrid: '" . $child . "'";
			if($param_name = $this->fieldData [$name]->getParamName()){
				$column[] = "childParam: '" . $param_name . "'";
			}
			$column = "{" . implode(',',$column) . "}";
			$columns[] = $column;
 		}
		$html .= implode(',',$columns);
		
		$html .= "],
			     //viewConfig: { forceFit: true},";
		
		$html .= $this->renderLinks();
					       
		$html .=   "
			        width:". $this->getGridWidth() .",					
			        height:550
			    });
			  ".(($this->renderAction)
						? (
						$this->getItem().".on('celldblclick', celldblclick_".$this->_action_index.");"
						.$this->getItem().".on('cellclick', cellclick_".$this->_action_index.");"
						.$this->getItem().".getSelectionModel().on('selectionchange', selectionchange_".$this->_action_index.");"
						.$this->getItem().".on('keypress', keypress_".$this->_action_index.");"
						)
						: "")
						;
		
		return $html;
	}
	
	public function renderCore (){
		$this->preRender();
		return $this->renderGrid();
	}
	
	public function renderBody() {
		$html = '{';
		$html .= 'success:true,';		
		$active_row = false;
		$data = $this->getData ();
		$html .= 'results:' . $data['all_count'] . ',';
		$field_count = count ( $this->fieldNames );
		
		$i = 1;
		$last_category = null;
		$html .= 'rows:[';
		$rows = array();
		foreach ( $data ['data'] as $row ) {
			$row_id = $row [ $this->rowIdName ];
			$row_html = array();
			if ($row_id == $this->activeRow) {
				
			} else {
				
			}
			
			foreach ( $this->fields as $name => $field ) {
				$field->row_id = $row_id;
				$row_tmp = '"' . $field->getName () . '" :';
				if ($field instanceof ArOn_Crud_Grid_Column) { 
					$row_tmp .=  ArOn_Crud_Tools_String::quote( $field->render ( $row ) ) ;
				} else {
					$row_tmp .= '""';
				}
				$row_html[] = $row_tmp;
			}
			$rows[] = "{" . implode(', ', $row_html) . "}";
			$i ++;
		}
		
		$html .= implode(',',$rows);
		$html .= ']';
		
		if ($active_row) {
			//						$active_row = str_replace('<tr class="record','<tr class="active',$active_row);
		//						$html = $active_row.$html;						
		} elseif ($this->activeRow) {
			$row_html = '';
			$select = clone $this->currentSelect;
			$select->reset ( 'where' );
			$select->reset ( 'order' );
			$select->limit ( 0 );
			$select->filterId ( $this->activeRow );
			
			$row = $this->table->fetchRow ( $select );
			if ($row !== null) {
				$row = $row->toArray ();
				$row_id = @$row [$this->rowIdName];				
				
			}
		}
		$html .= '}';
		return $html;
	}
	
	public function renderFoot() {}
	
	protected function renderTitle() {
		
		$html = '<script type="text/javascript">' . "\r\n";
				
		$html .= $this->_CRUD_NAME . ".mygrid = function() {";
		
		$html .= $this->renderGrid();
		
		$items = array();
		$items[] = $this->getItem();
		foreach ($this->_associate_with as $item){
			$html .= $item->render();
			$items[] = $item->getItem();
		} 
		
		switch ($this->_gridWindowStatus) {
			case 'normal':
				$mmwin = "win.show();";
				$mmconfig = "";
				break;
			case 'minimized':
				$mmwin = "win.minimized = true;";
				$mmconfig = "";
				break;
			case 'maximized':
				$mmwin = "win.show();";
				$mmconfig = "maximized: true,";
				break;
			default:
				$mmwin = "win.show();";
				$mmconfig = "";
				break;
		}
		
    	$html .= "
    	return {
	    	init: function() {
	    		var desktop = MyDesktop.getModule('grid-win-" . (($this->desktopName) ? $this->desktopName : $this->ajaxActionName) . "').app.getDesktop();
	    		var win = desktop.getWindow('" . $this->_win_id . "');
	    		var win_width = ".($this->_window_width+20).";
	    		var win_height = 430;
				var client_width = document.getElementById('x-desktop').clientWidth-50;
				var client_height = document.getElementById('x-desktop').clientHeight- 50;
				
				if (client_width < win_width){
					win_width = client_width;
				}
				if (client_height < win_height){
					win_height = client_height;
				}
	    		if(!win){
	    			win = desktop.createWindow({
	    				id:'grid-win-" . $this->ajaxActionName . "',
	    				title:'" . $this->windowTitle . "',
	    				height: win_height,
	    				width: win_width,
	    				iconCls:'icon-grid',
	    				shim:false,
	    				".$mmconfig."
	    				animCollapse:false,
	    				constrainHeader:true,
	    				layout:'border',
	    				forceLayout: true,
	    				items: [".implode(', ', $items)."]
	    			});
	    			setcookie_array('extjs_".self::$ajaxModuleName."_win','" . $this->ajaxActionName . "',true, 3600, '/');
	    			win.on('close', function(){
	    				removecookie_array('extjs_".self::$ajaxModuleName."_win','" . $this->ajaxActionName . "', 3600, '/')
					});
	    		}
	    		else{
	    			win.items.first().getStore().baseParams = ".$this->_store_var.'_'.$this->_action_index.".baseParams;
	    			win.items.first().getStore().load();
	    		}
	    		Ext.get('loading').hide();
	    		".(($this->_windowBlinking)?"win.startBlinking();":"")."
	    		".$mmwin."
			}
		};
	}();
    
		Ext.onReady(" . $this->_CRUD_NAME . ".mygrid.init, " . $this->_CRUD_NAME . ".mygrid);";
    	
		$html .= "\r\n" . '</script>';
		
		return $html;
		
	}
	
	protected function renderAction_create (){
		return "
					var b = Ext.merge(".$this->getItem().".getStore().baseParams);
					b.id = 0;
					if (b['dir']) delete b['dir'];
					if (b['sort']) delete b['sort'];
					if (b['limit']) delete b['limit'];
					Ext.get('loader').load({
						waitMsg:'Загрузка...',
						url:'/" . self::$ajaxModuleName . "/". (($this->editController) ? $this->editController : 'form' ) ."/" . (($this->editAction) ? $this->editAction : $this->ajaxActionName) . "/create',
						scripts:true,
						discardUrl: true,
						nocache: true,
						timeout: 5,
						params: b
					});";
	}
	
	protected function renderAction_edit (){
		return "
					var m = ".$this->getItem().".getSelectionModel().getSelections();
					if(m.length == 1) {
						Ext.get('loader').load({
							waitMsg:'Загрузка...',
							url:'/" . self::$ajaxModuleName . "/". (($this->editController) ? $this->editController : 'form' ) ."/" . (($this->editAction) ? $this->editAction : $this->ajaxActionName) . "/edit',
							scripts:true,
							discardUrl: true,
							nocache: true,
							timeout: 5,
							params: {'id': m[0].id}
						});
					} else {
						var err = '';
						if (m.length > 1) err = 'Выберите только один объект';
						else              err = 'Выберите объект для редактирования';
						Ext.MessageBox.alert('Информация', err);
					}";
	}
	
	protected function renderAction_remove (){
		return "
					var m = ".$this->getItem().".getSelectionModel().getSelections();
					if(m.length > 0) {
						Ext.MessageBox.confirm('Информация', 'Вы действительно хотите удалить выбранные объекты?' , function (btn) {
							if(btn == 'yes') {
								var jsonData = '[';
								for(var i = 0, len = m.length; i < len; i++) {
									var ss = '\"' + m[i].id + '\"';
									if(i==0) {
										jsonData = jsonData + ss;
									} else {
										jsonData = jsonData + ',' + ss;
									}
								}
								jsonData = jsonData + ']';
								Ext.Ajax.request({
									url: '/" . self::$ajaxModuleName . "/form/" . $this->ajaxActionName . "/remove',
									success: function(form, action) {
										var response = eval('(' + form.responseText + ')');
										if (response.success == false){
											var err = '<b>Невозможно получить данные!</b><hr />Возможно на стороне сервера произошла ошибка либо Вы неавторизированы.';
											if (response.errorMessage) err = \"<b>Ответ от сервера:</b><hr />\" + response.errorMessage;
											else if (response.message) err = '<b>Ответ от сервера:</b><hr />' + response.message;
											Ext.MessageBox.alert('Ошибка', err);
										} else {
											".$this->getItem().".getSelectionModel().each(function(){
												".$this->_store_var.'_'.$this->_action_index.".remove(this.getSelected());
											});
											Ext.MessageBox.alert('Информация', response.message);
										}
									},
									failure: function(form, action) {
										Ext.MessageBox.alert('Ошибка', 'На сервере произошла ошибка, либо сервер недоступен');
									},
									params: {
										'ids':jsonData
									}
								});
							}
						});
					} else {
						Ext.MessageBox.alert('Информация', 'Нет выбраных объектов');
					}";
	}
	
	protected function renderAction_duplicate (){
		return "
					var m = ".$this->getItem().".getSelectionModel().getSelections();
					if(m.length > 0) {
						Ext.MessageBox.confirm('Информация', 'Вы действительно хотите клонировать выбранные объекты?' , function (btn) {
							if(btn == 'yes') {
								var jsonData = '[';
								for(var i = 0, len = m.length; i < len; i++) {
									var ss = '\"' + m[i].id + '\"';
									if(i==0) {
										jsonData = jsonData + ss;
									} else {
										jsonData = jsonData + ',' + ss;
									}
								}
								jsonData = jsonData + ']';
								Ext.Ajax.request({
									url: '/" . self::$ajaxModuleName . "/form/" . $this->ajaxActionName . "/duplicate',
									success: function(form, action) {
										var response = eval('(' + form.responseText + ')');
										if (response.success == false){
											var err = '<b>Невозможно получить данные!</b><hr />Возможно на стороне сервера произошла ошибка либо Вы неавторизированы.';
											if (response.errorMessage) err = \"<b>Ответ от сервера:</b><hr />\" + response.errorMessage;
											else if (response.message) err = '<b>Ответ от сервера:</b><hr />' + response.message;
											Ext.MessageBox.alert('Ошибка', err);
										} else {
											".$this->_store_var.'_'.$this->_action_index.".reload();
											//Ext.MessageBox.alert('Информация', response.message);
										}
									},
									failure: function(form, action) {
										Ext.MessageBox.alert('Ошибка', 'На сервере произошла ошибка, либо сервер недоступен');
									},
									params: {
										'ids':jsonData
									}
								});
							}
						});
					} else {
						Ext.MessageBox.alert('Информация', 'Нет выбраных объектов');
					}";
	}
	
	protected function renderAction_dblclick (){
		return "edit_".$this->_action_index."();";
	}

	protected function renderAction_click (){
		return "";
	}
	protected function renderAction_selectionchange (){
		return "";
	}
	
	protected function renderAction_keypress (){
		return "";
	}
	
	public function renderAction() {
		if (empty($this->actions['dblclick'])){
			$this->actions['dblclick'] = array(
				'active' => true,
				'text'   =>'',
	    		'tooltip'=>'',
			);
		}
		
		$html = "
				var celldblclick_".$this->_action_index." = function (grid, rowIndex, columnIndex, obj ) {
					 var childGrid = grid.getColumnModel().config[columnIndex].childGrid;
				     var childParam = grid.getColumnModel().config[columnIndex].childParam;
				     if(childGrid !== undefined && childParam !== undefined){
				     	var gridstore = grid.getStore();
				     	var id = gridstore.getAt(rowIndex).id;
				     	childgrid_".$this->_action_index."(childGrid, childParam, id, gridstore);
				     }".($this->actions['dblclick']['active']?"
				     else {
				     	dblclick_".$this->_action_index."();
				     }":"")."
				};
				var cellclick_".$this->_action_index." = function (grid, rowIndex, columnIndex, obj ) {
				     ".($this->actions['click']['active']?"
				     	click_".$this->_action_index."();
				     ":"")."
				};
				
				var keypress_".$this->_action_index." = function (e) {".
					$this->renderAction_keypress()
				."};
				var selectionchange_".$this->_action_index." = function (o, s) {".
					$this->renderAction_selectionchange()
				."};
				
				var childgrid_".$this->_action_index." = function (childGrid, childParam, id, s) {
		        	//var params = new Object();
		        	var params = s.baseParams;
		        	params[childParam] = id;
		        	
			        Ext.get('loader').load({
						waitMsg:'Загрузка...',
						url:'/" . self::$ajaxModuleName . "/grid/'+ childGrid,
						scripts:true,
						discardUrl: true,
						nocache: true,
						timeout: 5,
						params: params
					});
				};";
		
		foreach($this->actions as $action_name=>$action){
			$html .= "var ".$action_name."_".$this->_action_index." = function () {";
			if ($action['active']) {;
				$render_func_name = "renderAction_".$action_name;
				$html .= $this->$render_func_name();
			}
			$html .= "};";
		}
		$this->actions['dblclick']['active'] = false;
		$this->actions['click']['active'] = false;
		
		return $html;
	}
	
	public function renderPaginator() {
		$limit = $this->getLimit();
		$limit_select = array ('10' => '10', '20' => '20', '50' => '50', '100' => '100', '200' => '200' );
		if ($limit == 'all') {
			$limit_select ['all'] = 'All';
		}
		
		
		if ($limit == 'all') {
			$html .= ' Total: ' . count ( $this->_data ['data'] );
			return $html;
		}
		
		if (! empty ( $url )) {
			$url = "&" . $url;
		}
		
		foreach ( $this->_data ['array_pages'] as $page => $exist ) {
			
			if ($page == 'prev' && $exist != 0) {
				// /'.$module.'/'.$this->controller.'/'.$this->action.'/						
				$html .= '<a href="?p=' . $exist . $url . '"' . $ajax_attr . '>&lt Previous</a>  | ';
			} elseif ($page == 'next' && $exist != 0) {
				$html .= '<a href="?p=' . $exist . $url . '"' . $ajax_attr . '>Next &gt</a> ';
			} elseif ($exist === 'now') {
				$html .= $page . ' | ';
			} elseif ($exist != 0 && $page != 'last' && $page != 'first') {
				$html .= '<a href="?p=' . $page . $url . '"' . $ajax_attr . '>' . $page . '</a>  | ';
			}
		
		}
		
	}
	
	protected function getPage() {
		if ($this->active_mode && isset ( $this->_params ['start'] )){
			$limit = $this->getLimit();
			if($limit == 'all')
				return 1;
			return ( ( $this->_params ['start']/ $this->getLimit() ) + 1) ;
		}
		else
			return @$this->default ['p'];
	}
	
	public function getItem(){
		return str_replace('-', '__', $this->_item_var.'_'.$this->ajaxActionName.'_'.$this->_action_index);
	}
	
	public function getWinId(){		
		return $this->_win_id;
	}
	
	public function setWinId($win_id){
		$this->_win_id = $win_id;
		return $this;
	}
}
