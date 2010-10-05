Ext.namespace('Ext.ux', 'Ext.ux.Utils');

Ext.ux.Utils.exists = function (varName){
	if (!Ext.isString(varName)) return Ext.isDefined(varName);
	var  d = varName.split(".");
	var n = '';
	for (var i in d){
		if (Ext.isString(d[i])){
			n = n+d[i];
			if (!Ext.isDefined(
					eval(n)
				)
			) return false;
			n = n+'.';
		}
	}
	return true;
};
Ext.merge = function (){
	var res = {};
	Ext.each(arguments, function(arg) {
		if('object' !== typeof arg) {
	        arg = [arg];
	    }
		var j;
		for (j in arg){
			if (typeof arg[j] == 'object'){
				res[j] = Ext.merge(arg[j]);
			}
			else {
				res[j] = arg[j];
			}
		}
	});
	return res;
};
Ext.override(Ext.form.HtmlEditor, {
	pluginsDefaultConfig:{
		'Ext.ux.plugins.HtmlEditorFullScreen': true,
		'Ext.ux.form.HtmlEditor.Word': true,
		'Ext.ux.form.HtmlEditor.Table': true,
		'Ext.ux.form.HtmlEditor.HR': true,
		'Ext.ux.form.HtmlEditor.IndentOutdent': true,
		'Ext.ux.form.HtmlEditor.SubSuperScript': true,
		'Ext.ux.form.HtmlEditor.RemoveFormat': true,
		'Ext.ux.form.HtmlEditor.SpecialCharacters': true,
		'Ext.ux.plugins.HtmlEditorImageInsert': true
	},
	
	pluginsConfig:{},
	
	disableItems: function(disabled){
	    if(this.fontSelect){
	        this.fontSelect.dom.disabled = disabled;
	    }
	    this.tb.items.each(function(item){
	        if(item.getItemId() != 'sourceedit' && item.alwaysEnabled !== true){
	            item.setDisabled(disabled);
	        }
	    });
	},
	initComponent : function(){
        this.addEvents(
            'initialize',
            'activate',
            'beforesync',
            'beforepush',
            'sync',
            'push',
            'editmodechange'
        );
        
        var pluginsConfig = Ext.merge(this.pluginsDefaultConfig, this.pluginsConfig);
        var plugins = [];
        
        for (var className in pluginsConfig){
        	if (pluginsConfig[className] && Ext.ux.Utils.exists(className)){
            	plugins.push(eval('new '+className+'(pluginsConfig[className])'));
            }
        }
        
        this.plugins = this.plugins || [];
        this.plugins = plugins.concat(this.plugins);
    }
});
Ext.override(Ext.FormPanel, {
	initEvents : function(){
	    if(this.keys){
	        this.getKeyMap();
	    }
	    if(this.draggable){
	        this.initDraggable();
	    }
	    if(this.toolbars.length > 0){
	        Ext.each(this.toolbars, function(tb){
	            tb.doLayout();
	            tb.on({
	                scope: this,
	                afterlayout: this.syncHeight
	            });
	            if (tb.defaultType != 'button'){
		            tb.on({
		                scope: this,
		                remove: this.syncHeight
		            });
	            }
	        }, this);
	        if(!this.ownerCt){
	            this.syncHeight();
	        }
	    }
	
	}
});