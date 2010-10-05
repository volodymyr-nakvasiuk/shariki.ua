/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.app.App = function(cfg){
    Ext.apply(this, cfg);
    this.addEvents({
        'ready' : true,
        'beforeunload' : true
    });

    Ext.onReady(this.initApp, this);
};

Ext.extend(Ext.app.App, Ext.util.Observable, {
    isReady: false,
    startMenu: null,
    modules: null,
    contextMenu: null,

    getStartConfig : function(){},
    
    getContextMenu: function (){},

    initApp : function(){
    	this.startConfig = this.startConfig || this.getStartConfig();
    	this.contextMenu = this.contextMenu || this.getContextMenu();

        this.desktop = new Ext.Desktop(this);

		this.launcher = this.desktop.taskbar.startMenu;

		this.modules = this.getModules();
        if(this.modules){
            this.initModules(this.modules);
			this.initMenu();

        }

        this.init();

        Ext.EventManager.on(window, 'beforeunload', this.onUnload, this);
		this.fireEvent('ready', this);
        this.isReady = true;
    },

    getModules : Ext.emptyFn,
    init : Ext.emptyFn,
    
    addContexMenu : function(menus, obj){
    	if (!obj) obj = this;
    	Ext.each(menus, function(m){
    		if (m.contextMenuOn){
	    		if (!m.listeners) m.listeners = {};
	    		m.listeners['afterrender'] = {
	    			fn: function(obj){
		 				obj.getEl().on('contextmenu',function(e,t,o){
		 					e.stopEvent();
		 					this.contextMenu = (this.contextMenu instanceof Ext.menu.Menu) ? this.contextMenu : new Ext.menu.Menu(Ext.apply({allowOtherMenus: true},this.contextMenu));
		 					var xy = e.getXY();
		 					var t = e.getTarget('.x-menu-item');
		 					if(t && t.id){
		 						this.contextMenu.callItem = Ext.getCmp(t.id);
		 					}
		 					var t = e.getTarget('.x-menu');
		 					var parentMenu = null;
		 					if(t && t.id){
		 						parentMenu = Ext.getCmp(t.id);
		 					}
		 					this.contextMenu.showAt(xy,parentMenu);
		 	 			},this);
	    			},
	    			scope: this
	    		};
    		}
    		if (m.menu){
    			if (m.menu.items){
    				m.menu.items = this.addContexMenu(m.menu.items, obj);
    			}
    		}
    	},obj);
    	return menus;
    },

	initMenu : function(){
		menus = this.getMenuConfig();
		if (this.contextMenu){
			menus = this.addContexMenu(menus);
		}
		for(var i = 0, len = menus.length; i < len; i++){
 			var item = this.launcher.add(menus[i]);
		}
	},

    initModules : function(ms){
		for(var i = 0, len = ms.length; i < len; i++){
            var m = ms[i];
            //this.launcher.add(m.launcher);
            m.app = this;
        }
    },
    
    getModule : function(name){
    	var ms = this.modules;
    	for(var i = 0, len = ms.length; i < len; i++){
    		if(ms[i].id == name || ms[i].appType == name){
    			return ms[i];
			}
        }
        return '';
    },

    onReady : function(fn, scope){
        if(!this.isReady){
            this.on('ready', fn, scope);
        }else{
            fn.call(scope, this);
        }
    },

    getDesktop : function(){
        return this.desktop;
    },

    onUnload : function(e){
        if(this.fireEvent('beforeunload', this) === false){
            e.stopEvent();
        }
    }
});