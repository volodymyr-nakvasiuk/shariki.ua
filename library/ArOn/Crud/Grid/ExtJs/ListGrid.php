<?php
class ArOn_Crud_Grid_ExtJs_ListGrid extends ArOn_Crud_Grid_ExtJs {

	public function renderLinks() {
		$limit = $this->getLimit();
		$html = "
		bbar: new Ext.PagingToolbar({
        	pageSize: " . $limit . ",
        	store: ".$this->_store_var.'_'.$this->_action_index.",
        	displayInfo:true,
        	displayMsg:'Отображаются записи {0} - {1} из {2}',
        	emptyMsg:'Нет записей для отображения'
        }),
        tbar:[{
    		text:'Удалить',
    		tooltip:'Удалить выделенные записи',
    		iconCls:'remove',
    		handler:remove_".$this->_action_index."
    	}, '-', ' Поиск:',
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
		})],";

		return $html;

	}

	public function renderAction() {

		$html = "
				var celldblclick_".$this->_action_index." = function (grid, rowIndex, columnIndex, obj ) {
					
				};
			
				var remove_".$this->_action_index." = function () {
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
					}
				};
				";
		return $html;
	}

}
