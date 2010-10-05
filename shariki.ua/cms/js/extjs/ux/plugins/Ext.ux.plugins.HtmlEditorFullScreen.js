Ext.namespace('Ext.ux', 'Ext.ux.plugins');

Ext.ux.plugins.HtmlEditorFullScreen = Ext.extend(Ext.util.Observable, {
	
    init: function(htmlEditor) {
        this.editor = htmlEditor;
        this.editor.on('render', this.onRender, this);
    },
    
    hndlr: function (){
		if (this.editor.maximazed === true){
			if (this.editor.parentEditor){
				this.editor.parentEditor.setValue(this.editor.getValue());
				this.editor.parentEditor.tb.get('htmlEditorFullScreen').setDisabled(false);
			}
			this.editor.ownerCt.hide();
		}
		else{
			this.editor.tb.get('htmlEditorFullScreen').setDisabled(true);
			if (!this.childWindow){
				this.childWindow = new Ext.Window({
	                title:'HTML редактор',
					minimizable: false,
	            	maximizable: false,
	            	closable: false,
	            	maximized: true,
	            	layout: 'border',
	            	forceLayout: true,
	            	closeAction: 'hide',
	            	cls: 'x-window-fullscreen',
	                items: [
						new Ext.form.HtmlEditor({
							maximazed: true,
							parentEditor: this.editor,
							region: 'center',
							pluginsConfig: Ext.merge(this.editor.pluginsConfig)
						})
	                ]
	            });
				var o = this.editor.ownerCt;
				while (o.baseCls){
					if (o.baseCls == 'x-window') break;
					else o = o.ownerCt;
				}
				if (o.baseCls == 'x-window'){
					o.on('close', function (){
						if (this.childWindow){
							this.childWindow.close();
						}
					},this);
				}
			}
			this.childWindow.items.get(0).setValue(this.editor.getValue());
            this.childWindow.show();
        }
	},
    
    onRender: function() {
        if (!Ext.isSafari) {
            this.editor.tb.insert(0, {
                itemId : 'htmlEditorFullScreen',
                iconCls : 'x-editor-full-screen',
                enableToggle: false,
                disabled: false,
                alwaysEnabled: true,
                pressed: this.editor.maximazed === true,
                scope: this,
                handler: this.hndlr,
                clickEvent:'mousedown',
                tooltip : {
                	title: 'Расширить редактор',
                	text: 'Расширить редактор на всю клиентскую часть браузера',
                	cls: 'x-html-full-screen-tip'
                },
                overflowText: 'Расширить редактор',
                tabIndex:-1
            });
        }
    }
});