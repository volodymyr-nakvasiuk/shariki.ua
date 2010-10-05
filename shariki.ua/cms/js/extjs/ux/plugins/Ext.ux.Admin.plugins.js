Ext.namespace('Ext.ux', 'Ext.ux.plugins');

Ext.ux.plugins.InsertServerImage = Ext.extend(Ext.util.Observable, {
	
	tpl: new Ext.XTemplate(
		'<tpl for=".">',
		'<div class="thumb-wrap" id="{optionValue}">',
		'<div class="thumb"><img src="{optionValue}" title="{displayText}"></div>',
		'</div>',
		'</tpl>'
	),
	
	emptyText: '<div style="padding:10px;">В папке нет флеш видео</div>',
	exts: 'jpg, jpeg, gif, png',
	title: 'Выберите картинку',
	winCls: 'x-window-insert-server-image',
	
	itemId : 'htmlEditorInsertServerImage',
    iconCls: 'x-editor-insert-server-image',
    
    tooltip : {
    	title: 'Вставить картинку из сервера',
    	text: 'Вставить картинку из сервера в HTML редактор',
    	cls: 'x-html-insert-server-image-tip'
    },
    
    overflowText: 'Вставить картинку из сервера',
	
	insertEditorObject: function (){
    	if (this.view){
    		var selNode = this.view.getSelectedNodes()[0];
            if(selNode){
            	if(Ext.isIE) range.select();
				this.editor.relayCmd('insertimage', selNode.id);
				this.winHide();
    		}
    	}
    },
    
    storeUrl: null,
	treeUrl: null,
	treeRoot: null,
	
	constructor: function (config){
		this.storeUrl = this.storeUrl || config.storeUrl;
		this.treeUrl = this.treeUrl || config.treeUrl;
		this.treeRoot = this.treeRoot || config.treeRoot;
	},
	
    init: function(htmlEditor) {
        this.editor = htmlEditor;
        this.editor.on('render', this.onRender, this);
    },
    
    winHide: function (){
    	if (this.childWindow){
    		this.childWindow.hide();
    	}
    },
    
    winClose: function (){
    	if (this.childWindow){
			this.childWindow.close();
		}
    },
    
    hndlr: function (){
    	if (!this.childWindow){
    		if (!this.view){
	    		this.view = new Ext.DataView({
	    			tpl: this.tpl,
	    			singleSelect: true,
	    			overClass:'x-view-over',
	    			itemSelector: 'div.thumb-wrap',
	    			emptyText: this.emptyText,
	    			store: new Ext.data.JsonStore({
	    				url: this.storeUrl,
	    				autoDestroy: true,
	    				root:'rows',
	    				idIndex: 0,
	    			    fields: ['optionValue', 'displayText'],
	    			    baseParams: {exts: this.exts}
	    			}),
	    			listeners: {
	    				'dblclick': {fn:this.insertEditorObject, scope:this}
	    			}
	    		});
    		}
    		
    		this.tree = new Ext.tree.TreePanel({
    	    	region: 'west',
    	    	split: true,
    	        useArrows: true,
    	        autoScroll: true,
    	        animate: true,
    	        containerScroll: true,
    	        border: false,
    	        width: 200,
    	        minWidth: 200,
    	        dataUrl: this.treeUrl,
    	        root: {
    	            nodeType: 'async',
    	            text: 'Выберите папку',
    	            draggable: false,
    	            id: this.treeRoot
    	        },
    	        tbar:[{
    	    		text:'Обновить',
    	    		tooltip:'Обновить дерево каталога',
    	    		iconCls:'refresh',
    	    		handler: function (o, e){
    	        		this.tree.getLoader().load(this.tree.getRootNode(), function(){
    	    				this.tree.getRootNode().expand();
    	        		},this);
    	    		},
    	    		scope: this
    	    	}]
    	    });
    		this.tree.on('click', function (node, obj){
                var store = this.view.store
                store.baseParams = store.baseParams || {};
            	store.baseParams['tree_val'] = node.id;
            	store.load();
    	    }, this);
    		this.tree.getRootNode().expand();
    		
	    	this.childWindow = new Ext.Window({
	    		title: this.title,
				minWidth: 200,
				width: 800,
				height: 300,
				minimizable: false,
	        	maximizable: false,
	        	closable: true,
	        	layout: 'border',
	        	forceLayout: true,
	        	closeAction: 'hide',
	        	cls: this.winCls,
				modal: true,
				items:[{
					id: 'img-chooser-view',
					region: 'center',
					autoScroll: true,
					items: this.view
				},this.tree],
				buttons: [{
					id: 'ok-btn',
					text: 'OK',
					handler: this.insertEditorObject,
					scope: this
				},{
					text: 'Отмена',
					handler: this.winHide,
					scope: this
				}],
				keys: {
					key: 27, // Esc key
					handler: this.winHide,
					scope: this
				}
	        });
			var o = this.editor.ownerCt;
			while (o.baseCls){
				if (o.baseCls == 'x-window') break;
				else o = o.ownerCt;
			}
			if (o.baseCls == 'x-window'){
				o.on('close', function (){
					this.winClose();
				},this);
			}
    	}
		this.childWindow.show();
    },
    
    onRender: function() {
        if (!Ext.isSafari) {
            this.editor.tb.add({
                itemId : this.itemId,
                iconCls: this.iconCls,
                enableToggle: false,
                disabled: false,
                alwaysEnabled: false,
                pressed: false,
                scope: this,
                handler: this.hndlr,
                clickEvent:'mousedown',
                tooltip: this.tooltip,
                overflowText: this.overflowText,
                tabIndex:-1
            });
        }
    }
});

Ext.ux.plugins.InsertServerFlash = Ext.extend(Ext.ux.plugins.InsertServerImage, {
	tpl: new Ext.XTemplate(
		'<tpl for=".">',
		'<div class="thumb-wrap" id="{optionValue}">',
		'<div class="thumb">'+
		"<object height=\"80px\" width=\"120px\" type=\"application/x-shockwave-flash\" data=\"/cms/swf/flowplayer-3.1.5.swf\">" +
			"<param value=\"true\" name=\"allowfullscreen\">" +
			"<param value=\"always\" name=\"allowscriptaccess\">" +
			"<param name=\"Movie\" value=\"/cms/swf/flowplayer-3.1.5.swf\"/>"+ 
			"<param name=\"Src\" value=\"/cms/swf/flowplayer-3.1.5.swf\"/>" +
			"<param value=\"high\" name=\"quality\">" +
			"<param value=\"false\" name=\"cachebusting\">" +
			"<param value=\"#000000\" name=\"bgcolor\">" +
			"<param name=\"wmode\" value=\"transparent\"/>"+ 
			"<param name=\"oop\" value=\"false\"/>"+
			"<param value=\"config={" +
			"	'clip':{" +
			"		'autoPlay':false," +
			"		'autoBuffering':false," +
			"		'url':'{optionValue}'" +
			"	}," +
			"	'plugins':{" +
			"		'controls':{" +
			"			'autoHide':'always'," +
			"			'hideDelay':2000," +
			"			'stop':true," +
			"			'mute':false," +
			"			'time':false," +
			"			'scrubber': false" +
			"		}" +
			"	}," +
			"	'playlist':[{" +
			"			'autoPlay':false," +
			"			'autoBuffering':false," +
			"			'url':'{optionValue}'" +
			"		}]" +
			"}\" name=\"flashvars\">" +
		"</object>"+
		
		'</div>',
		'</div>',
		'</tpl>'
	),
	
	emptyText: '<div style="padding:10px;">В папке нет флеш видео</div>',
	exts: 'flv',
	title: 'Выберите флеш-видео',
	winCls: 'x-window-insert-server-flash',
	
	itemId : 'htmlEditorInsertServerFlash',
    iconCls: 'x-editor-insert-server-flash',
    
    tooltip : {
    	title: 'Вставить флеш-видео из сервера',
    	text: 'Вставить флеш-видео из сервера в HTML редактор',
    	cls: 'x-html-insert-server-flash-tip'
    },
    
    overflowText: 'Вставить флеш-видео из сервера',
    
    insertEditorObject: function (){
    	if (this.view){
    		var selNode = this.view.getSelectedNodes()[0];
            if(selNode){
            	if(Ext.isIE) range.select();
				this.editor.insertAtCursor("{{flash_video_box url="+selNode.id.replace(" ","%20")+"}}");
				this.winHide();
    		}
    	}
    }
});