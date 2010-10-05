Ext.onReady(function() {
	Ext.QuickTips.init();
	Ext.form.Field.prototype.msgTarget = 'side';
	var loginfunc = function(){
		if (loginForm.form.isValid()) {
			loginForm.form.submit({
				waitMsg:'Идет загрузка...',
				url:'/client/login/',
				failure:function(form, action) {
					Ext.MessageBox.alert('Ошибка', action.result.msg);
				},
				success:function(form, action) {
					document.location = '/';
				}
			});
		} else {
			Ext.MessageBox.alert('Ошибка', 'Введите корректные данные.');
		}
	};
	
	var loginForm = new Ext.form.FormPanel({
		baseCls:'x-plain',
		labelWidth:115,
		labelAlign:'right',
		items:[
		new Ext.form.TextField({
			fieldLabel:'Войти',
			value:'',
			name:'login',
			allowBlank:false,
			width:100,
			listeners : {
    			'specialkey' : function(f, e){
        			if(e.getKey() == e.ENTER){
        				loginfunc();
        			}
    			}
			}
		}),
		new Ext.form.TextField({
			fieldLabel:'Пароль',
			value:'',
			name:'password',
			width:100,
			allowBlank:false,
			inputType:'password',
			listeners : {
    			'specialkey' : function(f, e){
        			if(e.getKey() == e.ENTER){
        				loginfunc();
        			}
    			}
			}
		}),
		new Ext.form.Checkbox({
			fieldLabel:'Запомнить',
			name:'save_login',
			inputValue:'yes',
			listeners : {
    			'specialkey' : function(f, e){
        			if(e.getKey() == e.ENTER){
        				loginfunc();
        			}
    			}
			}
		})]
	});
	var window = new Ext.Window({
		title:'Окно входа',
		width:300,
		height:160,
		resizable:false,
		bodyStyle:'padding:10px;',
		buttonAlign:'center',
		items:loginForm,
		buttons:[{
			text:'Логин',
			handler: loginfunc
		}]
	});
	Ext.get('loading').hide();
	window.show();
});
