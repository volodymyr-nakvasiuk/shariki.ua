<?php
class ArOn_Crud_Grid_ExtJs_TabForm extends ArOn_Crud_Grid_ExtJs_TabGrid {
	protected function renderButtons(){
		$html = ",";
		$html .= "
			buttons: [{
				text: 'Сохранить',
				handler: function(){
					var desktop = MyDesktop.getModule('grid-win-" . $this->ajaxActionName . "').app.getDesktop();
					var win = desktop.getWindow('" . $this->_win_id . "');
					var tabPanel = win.items.get(0);
					var items = tabPanel.items;
					var i;
					for (i=0;i<items.getCount();i++){
						var item = items.get(i);
						if (item.getXType()=='form'){
							document.extjsforms[item.name] = true;
							var form = item.getForm();
							if(form.isValid()){
								form.submit({
									url:'/" . ArOn_Crud_Form_ExtJs::$ajaxModuleName . "/form/'+item.phpData.actionName+'/save',
									waitMsg: 'Загрузка...',
									waitTitle: 'Пожалуйста подождите...',
									failure: function (form, action){
										if (action.result.message)
											Ext.MessageBox.alert('Ошибка', action.result.message);
										else
											Ext.MessageBox.alert('Ошибка', 'На сервере произошла ошибка, либо сервер недоступен');
									},
									success: function (form, action){
										document.extjsforms[form.name] = false;
										//Ext.MessageBox.hide();
										var desktop = MyDesktop.getModule(form.phpData._grid_id).app.getDesktop();
										var win = desktop.getWindow(form.phpData._parent_grid_id);
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
										var close = true;
										for(var i in document.extjsforms){
											if (document.extjsforms[i] && (typeof document.extjsforms[i] != 'function')){
												close = false;
												break;
											}
										}
										if (close){
											var win = desktop.getWindow(form.phpData._form_id);
											win.close();
										}
									}
								});
							}
							else {
								tabPanel.setActiveTab(item.id);
							}
						}
					}
				}
			},{
				text: 'Закрыть',
				handler: function(){
					var desktop = MyDesktop.getModule('grid-win-" . $this->ajaxActionName . "').app.getDesktop();
					var win = desktop.getWindow('" . $this->_win_id . "');
					win.close();
				}
			}]
			";
		return $html;
	}
}
