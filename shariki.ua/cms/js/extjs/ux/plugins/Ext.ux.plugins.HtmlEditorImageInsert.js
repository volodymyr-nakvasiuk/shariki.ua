Ext.namespace('Ext.ux', 'Ext.ux.plugins');

Ext.ux.plugins.HtmlEditorImageInsert = function(config) {
    
    config = config || {};
    
    Ext.apply(this, config);
    
    this.init = function(htmlEditor) {
        this.editor = htmlEditor;
        this.editor.on('render', onRender, this);
    };
    
    this.imageInsertConfig = {
    	popNotShow : config.popNotShow || false,
        popTitle: config.popTitle || 'URL фотограффии',
        popMsg: config.popMsg || 'Введите URL фото:',
        popWidth: config.popWidth || 400,
        popValue: config.popValue || '',
        dir: config.dir || '/cms/images',
        id: config.id || ''
    };
    
    this.imageInsert = function(){
        var range;
        if(Ext.isIE) range = this.editor.doc.selection.createRange();
        if (!this.imageInsertConfig.popNotShow){
        	Ext.MessageBox.show({
        		title: this.imageInsertConfig.popTitle,
        		msg: this.imageInsertConfig.popMsg,
        		width: this.imageInsertConfig.popWidth,
        		buttons: Ext.MessageBox.OKCANCEL,
        		prompt: true,
        		value: this.imageInsertConfig.popValue,
        		scope: this,
        		fn: function(btn, text) { 
        			if ( btn == 'ok' ) {
        				if(Ext.isIE) range.select();
        				this.editor.relayCmd('insertimage', text); 
        			}
        		}
        	});
        }
        else {
        	Ext.get('loader').load({
    			waitMsg:'Загрузка...',
    			url:'/cms/browse',
    			params: {
        			dir: this.imageInsertConfig.dir,
        			id: this.imageInsertConfig.id
        		},
    			scripts:true,
    			discardUrl: true,
    			nocache: true,
    			timeout: 5
    		});
        }
    };
    
    function onRender() {
        if (!Ext.isSafari) {
            this.editor.tb.add({
                itemId : 'htmlEditorImage',
                iconCls : 'x-btn-icon x-edit-insertimage',
                enableToggle: false,
                scope: this,
                handler:function(){ this.imageInsert(); },
                clickEvent:'mousedown',
                tooltip : config.buttonTip || {
                	title: 'Вставить картинку',
                	text: 'Вставить картинку в HTML редактор',
                	cls: 'x-html-editor-tip'
                },
                overflowText: 'Вставить картинку',
                tabIndex:-1
            });
        }
    }
};