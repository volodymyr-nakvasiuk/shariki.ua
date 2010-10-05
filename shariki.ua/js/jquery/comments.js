js.module("jquery.comments");
js.include("jquery.min");
js.include("jquery.template");
js.include("jquery.form");
js.include("jquery.validate");
$.fn.jComments = function (options){
	$.fn.loadComments = function(link){
		var comm_box = $(this);
		try{
			comm_box[0].o.preLoadComments(comm_box[0], link);
			if (comm_box[0].o.loading){
				$(comm_box[0].o.loading).show();
			}
			$.post(link, comm_box[0].o.post_params, function(response, status){
				var data = eval('({'+this.data.replace(/\&/g, '",').replace(/=/g, ':"')+'"})');
				var comm_box = $("#"+data.comm_id);
				if (status == 'success'){
					comm_box.setNewComments((response));
					comm_box[0].o.postLoadComments(comm_box[0], response);
				}
				if (comm_box[0].o.loading){
					$(comm_box[0].o.loading).hide();
				}
				
				});
			}
		catch(err){};
		
	};
	$.fn.setNewComments = function(json_data){
		if (!this[0].comm_in) return this;
		if (typeof json_data == 'string') json_data = eval("("+json_data+")");
		this[0].o.preSetComments(this, json_data.comments);
		var cc = 0;
		if (typeof json_data.comments == 'object'){
			if (this[0].o.comments_box){
				var comments_box = this.find(this[0].o.comments_box);
				comments_box.html("");
				for (i in json_data.comments){
					if (i != 'remove'){
						comments_box.append($.template(this[0].o.comment_template), json_data.comments[i]);
						cc++;
					}
				}
				if (cc==0 && this[0].o.noDataMsg) comments_box.append(this[0].o.noDataMsg);
			}
		}
		
		if (typeof json_data.message == 'string'){
			if (this[0].o.status_box){
				var $statusBox = $(this[0].o.status_box);
				if ($statusBox.height()==0) $statusBox.html('');
				var $div = $('<div style="display:none;"/>').html(json_data.message+"<hr/>").prependTo($statusBox);
				$div.slideDown('slow').wait(this[0].o.status_wait).hide('slow');
			}
		}
		
		if (typeof json_data.paging == 'object' && cc>0){
			if (this[0].o.paging_box){
				var paging_box = this.find(this[0].o.paging_box);
				paging_box.html("");
				var template;
				var page;
				var status;
				if (json_data.paging['first'] || json_data.paging['first']==0 || json_data.paging['first']==false){
					if (this[0].o.paging_template.first){
						paging_box.append($.template(this[0].o.paging_template.first), {page: json_data.paging['first'], status: (json_data.paging['first']&&json_data.paging['first']!="0")});
					}
					delete json_data.paging['first'];
				}
				if (json_data.paging['prev'] || json_data.paging['prev']==0 || json_data.paging['prev']==false){
					if (this[0].o.paging_template.prev){
						paging_box.append($.template(this[0].o.paging_template.prev), {page: json_data.paging['prev'], status: (json_data.paging['prev']&&json_data.paging['prev']!="0")});
					}
					delete json_data.paging['prev'];
				}
				for (var i in json_data.paging){
					template = null;
					page = json_data.paging[i];
					status = json_data.paging[i];
					switch(i) {
						case 'next':
							if (this[0].o.paging_template.next){
								template = this[0].o.paging_template.next;
							}
							break;
						case 'last':
							if (this[0].o.paging_template.last){
								template = this[0].o.paging_template.last;
							}
							break;
						default:
							page = i;
							status = json_data.paging[i];
							if (status == 'now'){
								status = "0";
							}
							if (this[0].o.paging_template.link){
								template = this[0].o.paging_template.link;
							}
							break;
					}
					if (template){
						paging_box.append($.template(template), {page: page, status: (status!="0")});
					}
				}
				paging_box.find("a").attr("comm_id", this[0].id).each(function(){
					var $this = $(this);
					var link = $this.attr("href");
					$this.attr("lnk", link).attr("href", "javascript:void(0);").click(function(){
						var link = $(this).attr("lnk", link);
						$("#"+$(this).attr("comm_id")).loadComments(link);
					});
				});
			}
		}
		this[0].o.postSetComments(this, json_data.comments);
		return this;
	};
	
	this[0].o = $.extend({
		form_showhide: null,
		form_template: "",
		form_box: null,
		form_variables: null,

		loading: null,
		
		form_target: null,
		form_url: '/',
		form_type: 'POST',
		form_beforeSubmit: function() {},
		form_success: function() {},
		form_dataType: null,
		form_semantic: false,
		form_resetForm: true,
		form_clearForm: false,
		form_iframe: false,
		
		validator_opt: {},
		
		comment_template: "",
		comments_box: null,
		comments: [],
		
		paging_template: {},
		paging_box: null,
		paging: {},
		
		post_params: {},
		
		preSetComments: function() {},
		postSetComments: function() {},
		
		preLoadComments: function() {},
		postLoadComments: function() {},
		
		status_box: null,
		status_wait: 3000,
		
		noDataMsg: false
	}, options || {});
	
	if (!this[0].id){
		this[0].id = "comm_"+Math.floor(Math.random()*100+1);
	}
	
	this[0].o.post_params['comm_id'] = this[0].id;
	
	if (this[0].o.form_box){
		var form_box = this.find(this[0].o.form_box);
		form_box.html("");
		var form = $("<form/>").append($.template(this[0].o.form_template), this[0].o.form_variables);
		form.ajaxForm({ 
			target: this[0].o.form_target,
			url: this[0].o.form_url,
			type: this[0].o.form_type,
			beforeSubmit: this[0].o.form_beforeSubmit,
			success: this[0].o.form_success,
			dataType: this[0].o.form_dataType,
			semantic: this[0].o.form_semantic,
			resetForm: this[0].o.form_resetForm,
			clearForm: this[0].o.form_clearForm,
			iframe: this[0].o.form_iframe,
			comm_id: this[0].id,
			complete: function(response, status){
				var comm_box = $("#"+this.comm_id);
				if (status == 'success'){
					comm_box.setNewComments((response.responseText));
					form.hide("slow");
				}
				if (comm_box[0].o.loading){
					$(comm_box[0].o.loading).hide();
				}
			}
		});
		
		form.find("input[type=submit]").each(function(){
			var tmp_div = $('<div style="display:none;"/>');
			$(this).after(tmp_div);
			tmp_div.append(this);
			var html = tmp_div.html();
			html = html.replace(/type\s*=\s*(\"|\'|\s*)submit(\"|\'|\s*)/gi, 'type=$1button$2');
			tmp_div.after(html).remove();
		});
			
		form.find("input[type=button]").attr("comm_id", this[0].id).click(function(){
			if (form.valid()){
				var comm_box = $("#"+$(this).attr("comm_id"));
				if (comm_box[0].o.loading){
					$(comm_box[0].o.loading).show();
				}
				form.submit();
			}
		});
		
		form.validate(this[0].o.validator_opt);
		
		form_box.append(form);
		
		if (this[0].o.form_showhide){
			form.css('display', 'none');
			$(this[0].o.form_showhide).click(function(){form.toggle("slow")});
		}
	}
	
	this[0].comm_in = true;
	this.setNewComments({comments: this[0].o.comments, paging: this[0].o.paging});
	return this;
};