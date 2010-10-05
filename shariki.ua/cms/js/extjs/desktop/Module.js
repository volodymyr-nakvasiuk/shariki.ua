/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.app.Module = function(config){
    Ext.apply(this, config);
    Ext.app.Module.superclass.constructor.call(this);
    this.init();
};

Ext.extend(Ext.app.Module, Ext.util.Observable, {
	init :function(){
		this.launcher = {
			iconCls:this.icon,
			text:this.title,
			handler:function (){
				this.createWindow();
			},
			contextMenuOn: true,
			scope:this,
			listeners: {
				'tests': false
			}
		}
	},
	createWindow:function(params){
		params = params || {};
		
		//Ext.get('loading').show();
		Ext.get('loader').load({
			waitMsg:'Загрузка...',
			url:this.url,
			scripts:true,
			discardUrl: true,
			nocache: true,
			timeout: 5,
			params: params
		});
	}
});