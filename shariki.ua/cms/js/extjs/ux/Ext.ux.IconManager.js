/**
 * author = Stanislav Golovenko
 * published = 2009.10.18
 * version = 0.4
 * extversion = 3.x
 * demo = http://code.google.com/p/extdim/w/list
 * forum = http://www.extjs.com/forum/showthread.php?t=83224
 * documentation = http://code.google.com/p/extdim/w/list
 */
 
 
Ext.ns('Ext.ux.Desktop');

Ext.ux.Desktop.Icon = Ext.extend(Ext.BoxComponent, {
	iconX: 0,
	iconY: 0,
	autoEl: {
		tag: 'dt'
	},
	IconsManager: null,
	style: 'position: absolute; z-index: 4000;',
	template: false,
	menu: false,
	dd: false,
	launchModuleId: false,
	elementDeletingFlag: false,
	DDGroup: 'iconsDDgroup',
	zindex: 4000,
	initComponent : function(){
			
		this.on('afterrender', function(c){
			
			this.initIcon();
			this.initDD();
			this.initSelectable();
			if(this.menu)
				this.initBaseMenu();
		}.createDelegate(this));
		
		Ext.ux.Desktop.Icon.superclass.initComponent.call(this);
		
		this.addEvents({
			"iconMove" : true,
			"iconSelect" : true,
			"iconDeselect" : true,
			"iconEnter" : true,
			"icondroppedon" : true,
			"iconBeforeDelete" : true
		})
	},
	initIcon : function() {
		
		this.getEl().setStyle('z-index', this.zindex);
		var template = new Ext.XTemplate(this.template);
		
		template.overwrite(this.getEl(), this);
		
		var coords = this.IconsManager.repairXY({iconX: this.iconX, iconY: this.iconY});
		this.iconX = coords.iconX;
		this.iconY = coords.iconY;
		this.IconsManager.placeIcon(this);
		this.getEl().on('dblclick', function(e,t,o){
			var icon = Ext.getCmp(this.id);
			if (icon.IconsManager.desktop && icon.launchModuleId){
				icon.IconsManager.desktop.getModule(icon.launchModuleId).createWindow()
			}
		});
	},
	initDD : function(){
		
		this.dd = new Ext.dd.DD(this.getEl(), this.DDGroup, {
	        isTarget  : true
	    });
	    
	    Ext.apply(this.dd, {
	    	startDrag : function(x, y){
	    		
				var icon = Ext.fly(this.getEl());
	    		var id = this.id;
	    		
	    		this.coordsBeforeDrag = icon.getXY();
	    		
	    		this.IconsManager.isDraggingElement = true;
	    		if(this.IconsManager.selectedIconsArray.indexOf(this.id) == -1){
	    			
	    			this.IconsManager.deselectAllIcons();
	    			this.IconsManager.selectIcon(id);
	    		}
				
		    	this.IconsManager.IconsGrid[this.iconY][this.iconX] = false;
	    	}.createDelegate(this),
		    endDrag : function(e) {
		    	
				e.stopEvent();
				
				if(!this.elementDeletingFlag){
					
					var el = Ext.get(this.getEl());
					
					var icon = this;					
					
					var elForPlace = this.IconsManager.repairXY({iconX: el.getStyle('left') + 'px', iconY: el.getStyle('top') + 'px'});
					icon.iconX = elForPlace.iconX;
					icon.iconY = elForPlace.iconY;
	
					this.IconsManager.placeIcon(icon);
					
					this.fireEvent('iconMove', icon);
					this.IconsManager.fireEvent('iconMove', this.IconsManager, icon);
					
					if(this.IconsManager.selectedIconsArray.length > 1){
						
						var newCoords = el.getXY();
						var oldCoords = this.coordsBeforeDrag;
						this.IconsManager.moveMoreThenOneSelectedIcon(icon.id, {
							iconX: newCoords[0] - oldCoords[0],
							iconY: newCoords[1] - oldCoords[1]
						});
					}
				
				}
			}.createDelegate(this),
			onDragEnter : function(evtObj, targetElId) {
			    // Colorize the drag target if the drag node's parent is not the same as the drop target
			    
			    if(this.id != targetElId){
			    	this.IconsManager.iconsArray[targetElId].fireEvent('iconEnter', this.IconsManager.iconsArray[targetElId], this);
			    }
			    
			}.createDelegate(this),
			onDragDrop : function(evtObj, targetElId) {
				
				if(this.id != targetElId){
			    	this.IconsManager.iconsArray[targetElId].fireEvent('icondroppedon', this.IconsManager.iconsArray[targetElId], this);
			    }
			}.createDelegate(this)
	    });
		
		var rentedDDTarget = new Ext.dd.DDTarget(this.getEl(), this.DDGroup);
	},
	initSelectable : function(){
		
		this.getEl().on('click', function(e){
    		e.stopEvent();
    		if(!e.ctrlKey){
				
				if(Ext.isIE){
					e.browserEvent.keyCode = 0;
				}
				if(!this.IconsManager.isDraggingElement)
					this.IconsManager.deselectAllIcons();
			}
			if(!this.IconsManager.isDraggingElement){
				
				if(e.ctrlKey){
					var icon = this;
					if(icon.selected)
						this.IconsManager.deselectIcon(this.id);
					else
						this.IconsManager.selectIcon(this.id);
				} else {
					this.IconsManager.selectIcon(this.id);
				}
			}
			this.IconsManager.isDraggingElement = false;
    	}.createDelegate(this));
		
	},
	initBaseMenu : function(){
		this.getEl().on('contextmenu', function(e){
			
			e.stopEvent();
			this.menu = (this.menu instanceof Ext.menu.Menu) ? this.menu : new Ext.menu.Menu(this.menu);
			if(!this.menu.el){
				this.menu.render();
			}
			var xy = e.getXY();
			
			this.menu.showAt(xy);
		}, this);
	},
	moveIconTo : function(XY, animate){ console.log('moveIconTo');
		var el = this.getEl();
		
		var oldCoords = el.getXY();
		
		var newCoords = this.IconsManager.repairXY({
			iconX: oldCoords[0] + parseInt(XY.iconX) + 'px',
			iconY: oldCoords[1] + parseInt(XY.iconY) + 'px'
		});
		this.IconsManager.IconsGrid[this.iconY][this.iconX] = false;
		el.moveTo(oldCoords[0] + XY.iconX, oldCoords[1] + XY.iconY, {
			
			callback : function(){
				
				this.iconX = newCoords.iconX;
				this.iconY = newCoords.iconY;
				
				this.IconsManager.placeIcon(this);
				this.fireEvent('iconMove', this);
				this.IconsManager.fireEvent('iconMove', this.IconsManager, this);
				
			}.createDelegate(this)
		});
	},
	selectIcon : function(){
		
		this.selected = true;
		
		var el = this.getEl();		
		var zindex = el.getStyle('z-index');
		el.setStyle('z-index', parseInt(zindex) + 5);
		if(this.IconsManager.iconsSelectionElement != ''){
			el = el.child(this.IconsManager.iconsSelectionElement);
		}
		
		if(!el.hasClass('ui-selected'))
			el.addClass('ui-selected');
		//new
		this.fireEvent('iconSelect', this);
		this.IconsManager.fireEvent('iconSelect', this.IconsManager, this);
	},
	deselectIcon : function(){
		
		this.selected = false;
		
		var el = this.getEl();
		
		var zindex = el.getStyle('z-index');
		
		el.setStyle('z-index', parseInt(zindex) - 5);
		
		if(this.IconsManager.iconsSelectionElement != '')
			el = el.child(this.IconsManager.iconsSelectionElement);		
		
		el.removeClass('ui-selected');
		//new
		this.fireEvent('iconDeselect', this);
		this.IconsManager.fireEvent('iconDeselect', this.IconsManager, this);
	},
	deleteIcon : function(){
		
		this.fireEvent('iconBeforeDelete', this);
		this.IconsManager.fireEvent('iconBeforeDelete', this.IconsManager, this);
		
		this.elementDeletingFlag = true;
		this.IconsManager.IconsGrid[this.iconY][this.iconX] = false;
		
		var indexToDelete = this.IconsManager.selectedIconsArray.indexOf(this.id);
		if(indexToDelete != -1)
			this.IconsManager.selectedIconsArray.splice(indexToDelete, 1);
		delete this.IconsManager.iconsArray[this.id];
		this.destroy();
		return true;
	}
});
Ext.reg('desktopicon', Ext.ux.Desktop.Icon);


/**
 * Creates new IconsManager
 * @constructor
 * @param {Object} config Configuration object
 */
Ext.ux.Desktop.IconsManager = Ext.extend(Ext.util.Observable, {
	/**
	 * @cfg {Object} desktop
	 * Desktop variable in your aplication
	 */
	desktop: null,
	/**
	 * @cfg {String} iconsArea
	 * Parent Element of all icon
	 */
	iconsArea: 'dl',
	/**
	 * @cfg {String} desktopEl
	 * Selector of desktop element
	 */
	desktopEl: '#x-desktop',
	/**
	 * @cfg {String} iconsSelectionElement
	 * Selector for icon selection view
	 */
	iconsSelectionElement: '',
	/**
	 * @cfg {String} iconsPostFix
	 * part of icon id that should be cut for detecting module thet should be launch
	 */
	iconsPostFix: '-shortcut',
	/**
	 * @cfg {Object} basicStyles
	 * styles for icons sectors
	 */
	basicStyles: {
		width: 88,
		height: 75
	},
	/**
	 * @cfg {Bool} basicTemplate
	 * icon template that should be use by default 
	 */
	basicTemplate:  '<a href="#"><img src="{icon}" />' +
			    	'<div>{text}</div></a>',
	firstLoad: true,
	constructor : function(config){
		
		Ext.apply(this, config);
		
		this.addEvents({
			//returns desktopIcon manager, icon record
            "iconSelect" : true,
            "iconDeselect" : true,
            "iconMove" : true,
			"iconAfterAdd" : true,
            "iconBeforeDelete" : true
        });
		
		this.initDesktopIconsConfiguration();
		this.initIcons();
		//this.initSelectable();
		
		Ext.EventManager.onWindowResize(function(){this.initDesktopIconsConfiguration()}, this);
		
		
		
		Ext.ux.Desktop.IconsManager.superclass.constructor.call(this);

	},	
	initIcons : function(){		
		
		var shortcuts = Ext.get(this.desktopEl.replace('#', ''));
		
		shortcuts.removeAllListeners();
		shortcuts.on('click', function (d){
			this.deselectAllIcons();
		}, this);

		this.isDraggingElement = false;
		this.selectedIconsArray = new Array();
		
	},
	initSelectable : function(){
		
		var desktopEl = Ext.get(this.desktopEl.replace('#', ''));
		var dragRegion = new Ext.lib.Region(0, 0, 0, 0);
		var bodyRegion = desktopEl.getRegion();
		var proxy, link = this;
		this.isDraggingElement = false;
		this.selectedIconsArray = new Array();

		tracker = new Ext.dd.DragTracker({
            onStart: function(){
				
		        if(!proxy){
		            proxy = desktopEl.createChild({cls:'x-view-selector'});
		        }else{
		            proxy.setDisplayed('block');
		        }
		        link.deselectAllIcons();
			},
			onDrag: function(){
				
				var startXY = tracker.startXY;
		        var xy = tracker.getXY();
		
		        var x = Math.min(startXY[0], xy[0]);
		        var y = Math.min(startXY[1], xy[1]);
		        var w = Math.abs(startXY[0] - xy[0]);
		        var h = Math.abs(startXY[1] - xy[1]);
		
		        dragRegion.left = x;
		        dragRegion.top = y;
		        dragRegion.right = x+w;
		        dragRegion.bottom = y+h;
		
		        dragRegion.constrainTo(bodyRegion);
		        proxy.setRegion(dragRegion);
		
		        for(var i in this.iconsArray){
		            var r = this.iconsArray[i].region;
					var sel = dragRegion.intersect(r);
		            if(sel && !r.selected){
		                r.selected = true;
						this.selectIcon(this.iconsArray[i].id);	
		            }else if(!sel && r.selected){
		                r.selected = false;
						this.deselectIcon(this.iconsArray[i].id);
		            }
		        }
			}.createDelegate(this),
			onEnd : function(){
				
				if(proxy){
		            proxy.setDisplayed(false);
		        }
			}
        });
        tracker.initEl(Ext.get(this.desktopEl.replace('#', '')));
        
	},
	
	repairXY : function(el){
		
		var ret = {};
		
		el.iconX = (el.iconX <= 0) ? 0 : el.iconX;
		el.iconY = (el.iconY <= 0) ? 0 : el.iconY;
		
		if(typeof el.iconX == 'string')
			ret.iconX = Math.round(parseInt(el.iconX) / this.basicStyles.width);
		else 
			ret.iconX = el.iconX;
			
		if(typeof el.iconY == 'string')
			ret.iconY = Math.round(parseInt(el.iconY) / this.basicStyles.height);
		else 
			ret.iconY = el.iconY;
		
		return ret;
	},
	
	initDesktopIconsConfiguration : function(){
		
		var DesktopWidth = Ext.fly(this.desktopEl.replace('#', '')).getWidth();
		var DesktopHeight = Ext.fly(this.desktopEl.replace('#', '')).getHeight();
		
		this.globalGrid = {};
		this.globalGrid.x = Math.floor(DesktopWidth / this.basicStyles.width);
		this.globalGrid.y = Math.floor(DesktopHeight / this.basicStyles.height);
		
		this.IconsGrid = new Array();
		for( var i = 0; i < this.globalGrid.y; i++){
			this.IconsGrid[i] = new Array();
			for( var h = 0; h < this.globalGrid.x; h++)
				this.IconsGrid[i][h] = false;
		}
		
//		var startDate = new Date();
//		for(var i = 0; i < 10000; i++)
//			$("#x-desktop dt");
//		var endDate = new Date();
//		console.log('(parseInt(endDate.getTime()) - parseInt(startDate.getTime()))');
//		alert((parseInt(endDate.getTime()) - parseInt(startDate.getTime())));
//		console.log(icons);
		if(!this.iconsArray){
			this.iconsArray = new Object();
			for (var i = 0; i < this.items.length; i++)
				this.iconsArray[this.items[i].id] = this.items[i];		
		}
		
		this.initManualIconsConfiguration(this.iconsArray);
		
	},
	initManualIconsConfiguration : function(icons){
			
		if(this.firstLoad){
			for(var i in icons){
				var item = icons[i];
				this.addIcon(item);
			}
		} else {
			
			for(var i in icons){
				var iconX = icons[i].iconX;
				var iconY = icons[i].iconY;
				this.placeIcon(icons[i]);
				var icon = this.iconsArray[i];
				if ((iconX != icon.iconX) || (iconY != icon.iconY)){
					this.iconsArray[i].fireEvent('iconMove', icon);
					this.fireEvent('iconMove', this, icon);
				}
			}
		}
		
		
		this.firstLoad = false;
	},
	placeIcon : function(config){
		
		if(config.iconX > this.globalGrid.x - 1)
			config.iconX = this.globalGrid.x - 1;
		if(config.iconY > this.globalGrid.y - 1)
			config.iconY = this.globalGrid.y - 1;
			
		if(config.iconX < 0)
			config.iconX = 0;
		if(config.iconY < 0)
			config.iconY = 0;
		
		//console.debug(this.IconsGrid);
		if(!this.IconsGrid[config.iconY][config.iconX]){
			//$('#' + config.id).css({top: config.iconY * this.basicStyles.height, left: config.iconX * this.basicStyles.width});
			var el = Ext.fly(config.id);
			this.IconsGrid[config.iconY][config.iconX] = true;
			
			el.moveTo(config.iconX * this.basicStyles.width, config.iconY * this.basicStyles.height);
			this.iconsArray[config.id].region = el.getRegion();
			this.iconsArray[config.id].iconX = config.iconX;
			this.iconsArray[config.id].iconY = config.iconY;
			
		} else {
			config.iconY++;
			if(config.iconY >= this.globalGrid.y){
				config.iconY = 0;
				config.iconX = config.iconX + 1;
			}
				
			this.placeIcon(config);
		}
		
	},
	selectIcon : function(id){
		
		this.iconsArray[id].selectIcon();
		this.selectedIconsArray.push(id);
	},
	deselectIcon : function(id){
		
		this.iconsArray[id].deselectIcon();
		this.selectedIconsArray.splice(this.selectedIconsArray.indexOf(id), 1);
	},
	deselectAllIcons : function(){
		var icons = Ext.apply([], this.getSelectedIconsIds());
		
		for(var i = 0; i < icons.length; i++){
			this.deselectIcon(icons[i]);
		}
	},
	getSelectedIcons : function(){
		
		var retArray = [];
		var iconsIds = this.getSelectedIconsIds();
		for(var i = 0; i < iconsIds.length; i++)
			retArray.push(this.iconsArray[iconsIds[i]]);
		
		return retArray;
	},
	getSelectedIconsIds : function(){
		
		return this.selectedIconsArray;
	},
	moveMoreThenOneSelectedIcon : function(currentId, XY){
		
		var selectedIcons = this.selectedIconsArray;
		for(var i = 0; i < selectedIcons.length; i++){
			if(currentId != selectedIcons[i])
				this.moveIconTo(selectedIcons[i], XY, true)
		}
	},
	moveIconTo : function(id, XY, animate){
		
		this.iconsArray[id].moveIconTo(XY, animate);
		
	},
	getIconCoordsById : function(id){
		
		if(this.iconsArray[id] != undefined)
			return this.iconsArray[id];
		
		return false;
	},
	SetIconsSortable : function(type){
		this.autoSort = type;
		this.initDesktopIconsConfiguration();
		this.autoSort = !type;
	},
	checkForDeletedIcons : function(icons){		
		
		for( var i in icons ){
			if(icons[i])
				Ext.fly(icons[i].id).setStyle('display', 'none');
		}
	},
	deleteIcon : function(id){
		return this.iconsArray[id].deleteIcon();
	},
	addIcon : function(item){

		if (!item.template){
			item.template = this.basicTemplate;
		}
		
		var iconsArea = Ext.fly(this.desktopEl.replace('#', ''));
		if(this.iconsArea)
			iconsArea = iconsArea.child(this.iconsArea);
		
		if(!item.IconsManager)
			item.IconsManager = this;
				
		this.iconsArray[item.id] = item instanceof Ext.ux.Desktop.Icon ? item : new Ext.ux.Desktop.Icon(item);
		this.iconsArray[item.id].render(iconsArea);
		
		this.fireEvent("iconAfterAdd", this, this.iconsArray[item.id]);
	}
});
Ext.reg('desktopiconmanager', Ext.ux.Desktop.IconsManager);