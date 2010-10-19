/****************************************************
* CKFinder Extension
*****************************************************/
Ext.ux.CKFinder = function(config){
	this.config = config;
	Ext.ux.CKFinder.superclass.constructor.call(this, config);
	/*
	this.on('destroy', function (ct) {
		ct.destroyInstance();
	});
	*/
};
Ext.extend(Ext.ux.CKFinder, Ext.BoxComponent, {
	onRender : function(ct, position){
		if(!this.el) {
			if (!this.config.CKConfig) this.config.CKConfig = {};
			var defConfig = {
				BasePath:'/js/ckfinder/',
				Id:'ckfinder' + this.id
			};
			Ext.apply(this.config.CKConfig, defConfig);
			if (!this.config.CKConfig.Id) this.config.CKConfig.Id = '/ckfinder/';

			var url = this.config.CKConfig.BasePath;
			var qs = "";

			if (!url) url = '/ckfinder/';
			if (url.charAt(url.length - 1) != '/') url += '/';
			url += 'ckfinder.html';

			if (this.config.CKConfig.SelectFunction) qs += ('?action=js&amp;func=' + this.config.CKConfig.SelectFunction);
			if (this.config.CKConfig.SelectFunctionData) {
				qs += (qs ? "&amp;" : "?");
				qs += ('data=' + encodeURIComponent(this.config.CKConfig.SelectFunctionData).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A'));
			}

			if (this.config.CKConfig.DisableThumbnailSelection) {
				qs += (qs ? "&amp;" : "?");
				qs += "dts=1";
			}
			else if (this.config.CKConfig.SelectThumbnailFunction || this.config.CKConfig.SelectFunction) {
				qs += (qs ? "&amp;" : "?");
				qs += ('thumbFunc=' + (this.config.CKConfig.SelectThumbnailFunction ? this.config.CKConfig.SelectThumbnailFunction : this.config.CKConfig.SelectFunction));

				if (this.config.CKConfig.SelectThumbnailFunctionData) qs += ('&amp;tdata=' + encodeURIComponent(this.config.CKConfig.SelectThumbnailFunctionData).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A'));
				else if (!this.config.CKConfig.SelectThumbnailFunction && this.config.CKConfig.SelectFunctionData) qs += ('&amp;tdata=' + encodeURIComponent(this.config.CKConfig.SelectFunctionData).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A'));
			}

			if (this.config.CKConfig.StartupPath) {
				qs += (qs ? "&amp;" : "?");
				qs += ("startupPath=" + encodeURIComponent(this.config.CKConfig.StartupPath + (this.config.CKConfig.StartupFolderExpanded ? ':1' : ':0')).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+'));
			}

			if (this.config.CKConfig.ResourceType) {
				qs += (qs ? "&amp;" : "?");
				qs += ("type=" + encodeURIComponent(this.config.CKConfig.ResourceType).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+'));
			}

			if (!this.config.CKConfig.RememberLastFolder) {
				qs += (qs ? "&amp;" : "?");
				qs += "rlf=0";
			}

			if (this.config.CKConfig.Id) {
				qs += (qs ? "&amp;" : "?");
				qs += ("id=" + encodeURIComponent(this.config.CKConfig.Id).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+'));
			}

			qs += (qs ? "&amp;" : "?");
			qs += "langCode=ru";

			this.el = ct.createChild({
				tag:'iframe',
				id:this.config.CKConfig.Id,
				scrolling:"no",
				frameBorder:0,
				src:url + qs,
				width:'100%',
				height: '100%'
			});
		}
		Ext.BoxComponent.superclass.onRender.call(this, ct, position);
	}/*,
	onResize: function(width, height) {
		Ext.form.TextArea.superclass.onResize.call(this, width, height);
		if (CKEDITOR.instances[this.id].is_instance_ready) {
			CKEDITOR.instances[this.id].resize(width, height);
		}
	},

	destroyInstance: function(){
		if (CKFinder.instances[this.id]) {
			delete CKFinder.instances[this.id];
		}
	}
	 */
});
Ext.reg('ckfinder', Ext.ux.CKFinder);