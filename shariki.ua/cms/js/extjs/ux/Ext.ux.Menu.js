Ext.override(Ext.menu.Menu, {
	MaxHeight:undefined,
	SaveState:false,
   
	onScrollTop:function(){
		this.ul.scroll("t",50,true);
	},
	onScrollBottom:function(){
		this.ul.scroll("b",50,true);
	}
	, showAt : function(xy, parentMenu, /* private: */_e){
        this.parentMenu = parentMenu;
        if(!this.el){
            this.render();
        }
        if(_e !== false){
            this.fireEvent("beforeshow", this);
            xy = this.el.adjustForConstraints(xy);
        }
	if(this.MaxHeight==undefined){
		var maxHeight=Ext.lib.Dom.getViewHeight()-xy[1];
	}
	else{
		maxHeight=this.MaxHeight
	}
	var last_ul_height=this.ul.getHeight();
	if(last_ul_height>maxHeight || this.scrolled==true){
		this.ul.setHeight(maxHeight-60);
		if(!this.SaveState){
			this.ul.scrollTo("top",1);
		}
	}	

	if(last_ul_height>maxHeight){
	var sb = this.el.createChild({
            tag: "div", cls: "menu_scroll_b"
        });
        sb.addClassOnOver('x-tab-scroller-left-over');
        this.TopRepeater = new Ext.util.ClickRepeater(sb, {
            interval : 200,
            handler: this.onScrollBottom,
            scope: this
        });
		var st = this.el.insertFirst({
           tag: "div", cls: "menu_scroll_top"
        });
        st.addClassOnOver('x-tab-scroller-left-over');
        this.leftRepeater = new Ext.util.ClickRepeater(st, {
            interval : 200,
            handler: this.onScrollTop,
            scope: this
        });
	

        this.scrollBottom = sb;	
	this.scrollTop = st;
	this.scrolled=true;
	}
        this.el.setXY(xy);
        this.el.show();
        this.hidden = false;
        this.focus();
        this.fireEvent("show", this);
    }
});