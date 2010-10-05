Ext.BLANK_IMAGE_URL = '/cms/js/extjs/resources/images/default/s.gif';

var defaultLang = 'ru';
Ext.MessageBox.buttonText.yes = 'Да';
Ext.MessageBox.buttonText.no = 'Нет';
//Ext.MessageBox.buttonText.ok = 'Да';
Ext.MessageBox.buttonText.cancel = 'Отмена';
Ext.WindowMgr.zseed = 50000;

//create namespace

Ext.namespace('crud');
// Desktop
MyDesktop = new Ext.app.App({
	init :function(){
		Ext.get('loader').getUpdater().on({
			'beforeupdate':function(el, obj, params){
				Ext.get('loading').show();
			},
			'update':function(el, action){
				Ext.get('loading').hide();
				if (action && action.responseText && action.responseText[0] == '{'){
					var response = eval('(' + action.responseText + ')');
					//Ext.MessageBox.alert('Ошибка', response.success);
					if (response.success == false){
						var err = '<b>Невозможно получить данные!</b><hr />Возможно на стороне сервера произошла ошибка либо Вы неавторизированы.';
						if (response.errorMessage) err = "<b>Ответ от сервера:</b><hr />" + response.errorMessage;
						else if (response.message) err = '<b>Ответ от сервера:</b><hr />' + response.message;
						Ext.MessageBox.alert('Ошибка', err);
					}
				}
			},
			'failure':function(el, action){
				Ext.get('loading').hide();
				Ext.MessageBox.alert('Ошибка', "<b>Невозможно получить данные!</b><hr />Возможно на стороне сервера произошла ошибка либо сервер недоступен.");
			}
		});
		Ext.QuickTips.init();
	},
	getModules :function(){
			return [
					new Ext.app.Module({
						id:'grid-win-price',
						title:'Мои автомобили',
						url:'/client/grid/price',
						icon:'bogus'
					}),
					new Ext.app.Module({
						id:'grid-win-nprice',
						title:'Мои новые автомобили',
						url:'/client/grid/nprice',
						icon:'bogus'
					}),
					new Ext.app.Module({
						id:'grid-win-category',
						title:'Категории автомобилей',
						url:'/client/grid/category',
						icon:'bogus'
					}),
					new Ext.app.Module({
						id:'grid-win-aprice',
						title:'Агенты',
						url:'/client/grid/aprice',
						icon:'bogus'
					}),
					new Ext.app.Module({
						id:'grid-win-bookmarks',
						title:'Избранные',
						url:'/client/grid/bookmarks',
						icon:'bogus'
					}),
					new Ext.app.Module({
						id:'grid-win-cmp',
						title:'Сравнения',
						url:'/client/grid/cmp',
						icon:'bogus'
					})
			       ];
	},
	getMenuConfig :function(){
		return [
		        this.getModule('grid-win-price').launcher,
		        this.getModule('grid-win-nprice').launcher,
		        this.getModule('grid-win-aprice').launcher,
		        this.getModule('grid-win-bookmarks').launcher,
		        this.getModule('grid-win-cmp').launcher
		       ]
	},
	getStartConfig :function(){
		return {
			title:'Shariki.UA - Menu',
			iconCls:'user',
			toolItems:[{
				text:'К сайту',
				iconCls:'site',
				scope:this,
				handler:function () {
				document.location = '/';
			}
			}, '-', {
				text:'Выход',
				iconCls:'logout',
				scope:this,
				handler:function () {
					document.location = '/client/login/logout';
				}
			}]
		};
	}
});