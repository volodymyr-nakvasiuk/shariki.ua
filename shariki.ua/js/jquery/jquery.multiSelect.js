var o = {};
if(jQuery) (function($){
	
	$.extend($.fn, {
		multiSelect: function(o, callback) {
			// Default options
			if( !o ) o = {};
			if( o.selectAll == undefined ) o.selectAll = true;
			if( o.unSelectAll == undefined ) o.unSelectAll = false;
			if( o.selectAllText == undefined ) o.selectAllText = "Select All";
			if( o.noneSelected == undefined ) o.noneSelected = 'Select options';
			if( o.oneOrMoreSelected == undefined ) o.oneOrMoreSelected = '% selected';
			
			// Initialize each multiSelect
			$(this).each( function() {
				var select = $(this);
				var setting = $(select).attr('setting');
				var tabindex = $(select).attr('tabindex');
				var html = '<input type="text" readonly="readonly" class="multiSelect" value="" style="cursor: default;" tabindex='+tabindex+'/>';
				html += '<div class="multiSelectOptions" style="position: absolute; z-index: 99999; display: none;" setting="'+setting+'">';				
				var name = $(select).attr('name');
				var id = $(select).attr('name');
				id = str_replace('[]','',id);
				id = str_replace('[','_',id);
				id = str_replace(']','',id);
				name += '[]';
				if( o.selectAll ) html += '<div class="filter_small_label"><input type="checkbox" class="selectAll" exemplar="multi" field="' + id + '"  id ="' + id + '_all"/><label class="selectAll">' + o.selectAllText + '</label></div>';
				$(select).find('OPTION').each( function() {
					if( $(this).val() != '' && $(this).val() != 0) {						
						html += '<div class="filter_small_label" id ="' + id + '_' + $(this).val() +'"><input type="checkbox" exemplar="multi" name="' + name + '" value="' + $(this).val() + '"';
						if( $(this).attr('selected') ) html += ' checked="checked"';
						html += ' /><label>' + $(this).html() + '</label></div>';
					}
				});
				html += '</div>';
				$(select).after(html);
				
				// Events
				$(select).next('.multiSelect').mouseover( function() {
					$(this).addClass('hover');
				}).mouseout( function() {
					$(this).removeClass('hover');
				}).click( function() {
					// Show/hide on click
					if( $(this).hasClass('active') ) {
						$(this).multiSelectOptionsHide();
					} else {
						$(this).multiSelectOptionsShow();
					}
					return false;
				}).focus( function() {
					// So it can be styled with CSS
					$(this).addClass('focus');
				}).blur( function() {
					// So it can be styled with CSS
					$(this).removeClass('focus');
				});
				
				// Determine if Select All should be checked initially
				if( o.selectAll ) {
					var sa = true;
					$(select).next('.multiSelect').next('.multiSelectOptions').find('INPUT:checkbox').not('.selectAll').each( function() {
						if( !$(this).attr('checked') ) sa = false;
					});
					if( sa ) $(select).next('.multiSelect').next('.multiSelectOptions').find('INPUT.selectAll').click().parent().addClass('checked');
				}
				
				// Handle Select All
				
				$(select).next('.multiSelect').next('.multiSelectOptions').find('INPUT.selectAll').click( function() {
					if( $(this).attr('checked') == true && o.unSelectAll == false){
						$(this).parent().parent().find('INPUT:checkbox').attr('checked', true).parent().addClass('checked');
					} else {
						$(this).parent().parent().find('INPUT:checkbox').attr('checked', false).parent().removeClass('checked').show('slow');
					}
				});
				
				// Handle checkboxes
				$(select).next('.multiSelect').next('.multiSelectOptions').find('INPUT:checkbox').click( function() {
					$(this).parent().parent().multiSelectUpdateSelected(o);
					$(this).parent().parent().find('LABEL').removeClass('checked').find('INPUT:checked').parent().addClass('checked');
					$(this).parent().parent().prev('.multiSelect').focus();
					if( !$(this).attr('checked') ) $(this).parent().parent().find('INPUT:checkbox.selectAll').attr('checked', false).parent().removeClass('checked');
					if( callback ) callback($(this));
					var agt = navigator.userAgent.toLowerCase();
					if(agt.indexOf("msie") != -1){
						//if (agt.substr(agt.indexOf("msie")+5,1) == '6'){
							$(this).change();
						//}
					}
				});
				
				// Initial display
				$(select).next('.multiSelect').next('.multiSelectOptions').each( function() {
					$(this).multiSelectUpdateSelected(o);
					$(this).find('INPUT:checked').parent().addClass('checked');
				});
				
				// Handle hovers
				$(select).next('.multiSelect').next('.multiSelectOptions').find('LABEL').click(function(){
					$(this).parent().find('INPUT:checkbox').each(function(){
						if ($(this).hasClass('selectAll')){
							$(this).click().change();
							$(this).attr('checked',false);
						} else {
							/*
							if(!$(this).attr('checked')) $(this).attr('checked',true).change();
							else $(this).attr('checked',false).change();
							*/
							$(this).attr('checked',!$(this).attr('checked')).click().attr('checked',!$(this).attr('checked'));
							var agt = navigator.userAgent.toLowerCase();
							var ie = agt.indexOf("msie");
							if (ie == -1){
								$(this).change();
							}
							$(this).parent().parent().multiSelectUpdateSelected(o);
						}
					});
				}).mouseover( function() {
					$(this).parent().find('LABEL').removeClass('hover');
					$(this).addClass('hover');
				}).mouseout( function() {
					$(this).parent().find('LABEL').removeClass('hover');
				});
				 
				// Keyboard
				$(select).next('.multiSelect').keydown( function(e) {
					// Is dropdown visible?
					if( $(this).next('.multiSelectOptions').is(':visible') ) {
						// Dropdown is visible
						// Tab
						if( e.keyCode == 9 ) {
							$(this).addClass('focus').trigger('click'); // esc, left, right - hide
							$(this).focus().next(':input').focus();
							return true;
						}
						
						// ESC, Left, Right
						if( e.keyCode == 27 || e.keyCode == 37 || e.keyCode == 39 ) {
							// Hide dropdown
							$(this).addClass('focus').trigger('click');
						}
						// Down
						if( e.keyCode == 40 ) {
							if( !$(this).next('.multiSelectOptions').find('LABEL').hasClass('hover') ) {
								// Default to first item
								$(this).next('.multiSelectOptions').find('LABEL:first').addClass('hover');
							} else {
								// Move down, cycle to top if on bottom
								$(this).next('.multiSelectOptions').find('LABEL.hover').removeClass('hover').next('LABEL').addClass('hover');
								if( !$(this).next('.multiSelectOptions').find('LABEL').hasClass('hover') ) {
									$(this).next('.multiSelectOptions').find('LABEL:first').addClass('hover');
								}
							}
							
							// Adjust the viewport if necessary
							$(this).multiSelectAdjustViewport($(this) );
							
							return false;
						}
						// Up
						if( e.keyCode == 38 ) {
							if( !$(this).next('.multiSelectOptions').find('LABEL').hasClass('hover') ) {
								// Default to first item
								$(this).next('.multiSelectOptions').find('LABEL:first').addClass('hover');
							} else {
								// Move up, cycle to bottom if on top
								$(this).next('.multiSelectOptions').find('LABEL.hover').removeClass('hover').prev('LABEL').addClass('hover');
								if( !$(this).next('.multiSelectOptions').find('LABEL').hasClass('hover') ) {
									$(this).next('.multiSelectOptions').find('LABEL:last').addClass('hover');
								}
							}
							
							// Adjust the viewport if necessary
							$(this).multiSelectAdjustViewport($(this) );
							
							return false;
						}
						// Enter, Space
						if( e.keyCode == 13 || e.keyCode == 32 ) {
							// Select All
							if( $(this).next('.multiSelectOptions').find('LABEL.hover INPUT:checkbox').hasClass('selectAll') ) {
								if( $(this).next('.multiSelectOptions').find('LABEL.hover INPUT:checkbox').attr('checked') ) {
									// Uncheck all
									$(this).next('.multiSelectOptions').find('INPUT:checkbox').attr('checked', false).parent().removeClass('checked');
								} else {
									// Check all									
									$(this).next('.multiSelectOptions').find('INPUT:checkbox').attr('checked', true).parent().addClass('checked');
								}
								$(this).next('.multiSelectOptions').multiSelectUpdateSelected(o);
								if( callback ) callback($(this));
								return false;
							}
							// Other checkboxes
							if( $(this).next('.multiSelectOptions').find('LABEL.hover INPUT:checkbox').attr('checked') ) {
								// Uncheck
								$(this).next('.multiSelectOptions').find('LABEL.hover INPUT:checkbox').attr('checked', false);
								$(this).next('.multiSelectOptions').multiSelectUpdateSelected(o);
								$(this).next('.multiSelectOptions').find('LABEL').removeClass('checked').find('INPUT:checked').parent().addClass('checked');
								// Select all status can't be checked at this point
								$(this).next('.multiSelectOptions').find('INPUT:checkbox.selectAll').attr('checked', false).parent().removeClass('checked');
								if( callback ) callback($(this));
							} else {
								// Check
								$(this).next('.multiSelectOptions').find('LABEL.hover INPUT:checkbox').attr('checked', true);
								$(this).next('.multiSelectOptions').multiSelectUpdateSelected(o);
								$(this).next('.multiSelectOptions').find('LABEL').removeClass('checked').find('INPUT:checked').parent().addClass('checked');
								if( callback ) callback($(this));
							}
						}
						return false;
					} else {
						// Dropdown is not visible
						if( e.keyCode == 38 || e.keyCode == 40 || e.keyCode == 13 || e.keyCode == 32 ) { // down, enter, space - show
							// Show dropdown
							$(this).removeClass('focus').trigger('click');
							$(this).next('.multiSelectOptions').find('LABEL:first').addClass('hover');
							return false;
						}
						//  Tab key
						if( e.keyCode == 9 ) {
							// Shift focus to next INPUT element on page
							$(this).focus().next(':input').focus();
							return true;
						}
					}
					// Prevent enter key from submitting form
					if( e.keyCode == 13 ) return false;
				});
				
				// Eliminate the original form element
				$(select).remove();
			});
			
		},
		
		// Hide the dropdown
		multiSelectOptionsHide: function() {
			$(this).removeClass('active').next('.multiSelectOptions').hide();
			//if IE6 show year_field
			var agt = navigator.userAgent.toLowerCase();
			var stng = $(this).next().attr('setting');
			if ((stng == 'region') || (stng == 'model')){
				if(agt.indexOf("msie") != -1){
					if (agt.substr(agt.indexOf("msie")+5,1) == '6'){
						$('#filter_1_year_from').css("visibility", "visible");
						$('#filter_1_year_to').css("visibility", "visible");
						$('#filter_1_currency').css("visibility", "visible");
					}
				}
			}
		},
		
		// Show the dropdown
		multiSelectOptionsShow: function() {
			// Hide any open option boxes
			$('.multiSelect').multiSelectOptionsHide();
			$(this).next('.multiSelectOptions').find('LABEL').removeClass('hover');
			$(this).addClass('active').next('.multiSelectOptions').show();
			
			// Position it
			var offset = $(this).position();
			$(this).next('.multiSelectOptions').css({ top:  offset.top + $(this).outerHeight() + 'px' });
			$(this).next('.multiSelectOptions').css({ left: offset.left + 'px' });
			
			// Disappear on hover out
			multiSelectCurrent = $(this);
			var timer = '';
			$(this).next('.multiSelectOptions').hover( function() {
				clearTimeout(timer);
			}, function() {
				timer = setTimeout('jQuery(multiSelectCurrent).multiSelectOptionsHide(); $(multiSelectCurrent).unbind("hover");', 250);
			});
			//if IE6 hide year_field
			var agt = navigator.userAgent.toLowerCase();
			var stng = $(this).next().attr('setting');
			if ((stng == 'region') || (stng == 'model')){
				if(agt.indexOf("msie") != -1){
					if (agt.substr(agt.indexOf("msie")+5,1) == '6'){
						$('#filter_1_year_from').css("visibility", "hidden");
						$('#filter_1_year_to').css("visibility", "hidden");
						$('#filter_1_currency').css("visibility", "hidden");
					}
				}
			}
		},
		
		// Update the textbox with the total number of selected items
		multiSelectUpdateSelected: function(o) {
			var i = 0, s = '';
			$(this).find('INPUT:checkbox:checked').not('.selectAll').each( function() {
				i++;
			});
			if( i == 0 ) {
				$(this).prev('INPUT.multiSelect').val( o.noneSelected );
			} else {
				if( o.oneOrMoreSelected == '*' ) {
					var display = '';
					$(this).find('INPUT:checkbox:checked').each( function() {
						if( $(this).parent().text() != o.selectAllText ) display = display + $(this).parent().text() + ', ';
					});
					display = display.substr(0, display.length - 2);
					$(this).prev('INPUT.multiSelect').val( display );
				} else {
					$(this).prev('INPUT.multiSelect').val( o.oneOrMoreSelected.replace('%', i) );
				}
			}
		},
		
		// Ensures that the selected item is always in the visible portion of the dropdown (for keyboard controls)
		multiSelectAdjustViewport: function(el) {
			// Calculate positions of elements
			var i = 0;
			var selectionTop = 0, selectionHeight = 0;
			$(el).next('.multiSelectOptions').find('LABEL').each( function() {
				if( $(this).hasClass('hover') ) { selectionTop = i; selectionHeight = $(this).outerHeight(); return; }
				i += $(this).outerHeight();
			});
			var divScroll = $(el).next('.multiSelectOptions').scrollTop();
			var divHeight = $(el).next('.multiSelectOptions').height();
			// Adjust the dropdown scroll position
			$(el).next('.multiSelectOptions').scrollTop(selectionTop - ((divHeight / 2) - (selectionHeight / 2)));
		}
		
	});
	
})(jQuery);