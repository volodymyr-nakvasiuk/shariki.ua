// IFrame
Ext.ux.IFrame = Ext.extend(Ext.BoxComponent, {
	onRender : function(ct, position){
		this.el = ct.createChild({
			tag:'iframe',
			id:'framepanel'+this.id,
			frameBorder:0,
			src:this.url,
			width:'100%',
			height: '100%'
		});
	}
});