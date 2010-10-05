/**
 * Provides a drop down field with multiple checkboxes
 * @author Tony Landis http://www.tonylandis.com/
 * @copyright Free for all use and modification. The author and copyright must be remain intact here.
 *
 * @class Ext.form.MultiSelectField
 * @extends Ext.form.TriggerField
 */
Ext.form.ImageMultiSelectField = Ext.extend(Ext.form.MultiSelectField,  {
	
	imageDir: '',
	imageStyle: '',
    
    renderValues : function(){
    	this.menu.removeAll();
    	if (this.clearAllRender){
    		this.menu.add(
    			new Ext.menu.CheckItem({
    				text: this.cleaAllText,
    				value: -1, 
    				hideOnClick: false 
    			})
    		).on({
					'click': {
						fn: this.clearAllHandler,
						scope: this
					},
					'checkchange': {
						fn: function (){
							this.setChecked(false);
						}
					}  
				});
    	}
    	this.store.each(function(r) {
    		if (this.groupField){
    			if (this.groupName != r.data[this.groupField]){
    				this.groupName = r.data[this.groupField];
    				this.menu.add({text: '<b>'+this.groupName+'</b>', hideOnClick: false});
    			}
    		}
    		this.menu.add(
    			new Ext.menu.CheckItem({
    				text: r.data[this.displayField],
    				value: r.data[this.valueField], 
    				hideOnClick: false 
    			})
    		).on('click', this.clickHandler, this);
    		this.menu.add({text: '<img style="'+this.imageStyle+'" src="'+this.imageDir+'/'+r.data[this.valueField]+'" />', hideOnClick: false});
    	}, this);
    }
});

Ext.reg('imagemultiselect', Ext.form.ImageMultiSelectField);