js.module("funcs");
js.include("jquery.min");
var driver_loaded_functions = {};
var _gaq = _gaq || [];

function debug(data){ console.log(data); }
String.prototype.isJSON = function () {
	var str = this;
	if (str.blank()) return false;
	str = str.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@');
	str = str.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']');
	str = str.replace(/(?:^|:|,)(?:\s*\[)+/g, '');
	return (/^[\],:{}\s]*$/).test(str);
};
String.prototype.empty = function() {
	return this == '';
};
String.prototype.blank = function() {
	return /^\s*$/.test(this);
};
function dec2bin(num){
	var bin = [];
	var dec = num;
	while (num>1){
		bin.push(num % 2);
		num = parseInt(num/2);
	}
	bin.push(num);
	bin = bin.reverse();
	return {'dec':dec, 'str':bin.join(''), 'arr':bin};
}
function isset () {
	var a=arguments, l=a.length, i=0;
	
	if (l===0) {
		throw new Error('Empty isset'); 
	}
	
	while (i!==l) {
		if (typeof(a[i])=='undefined' || a[i]===null) { 
			return false; 
		} else { 
			i++; 
		}
	}
	return true;
}
function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft;
		curtop = obj.offsetTop;
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		}
	}
	return [curleft,curtop];
}
var agt = navigator.userAgent.toLowerCase();
function strToInt(s) {
	return isNaN(v = parseInt(s)) ? 0 : v;
}
function setcookie(name, value, expires, path, domain, secure) {
	return this.setrawcookie(name, encodeURIComponent(value), expires, path, domain, secure);
}
function getcookie(c_name){
	if (document.cookie.length>0){
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1){
			c_start=c_start + c_name.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1) c_end=document.cookie.length;
			return unescape(document.cookie.substring(c_start,c_end));
		}
	}
	return "";
}
function removecookie( name, path, domain ) {
	if ( getcookie( name ) ) document.cookie = name + "=" +
	( ( path ) ? ";path=" + path : "") +
	( ( domain ) ? ";domain=" + domain : "" ) +
	";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}
function getcookie_array(c_name){
	var arr_json = getcookie(c_name);
	if (!arr_json){
		return {};
	}
	else {
		return eval('('+arr_json+')');
	}
}
function setcookie_array(name, key, value, expires, path, domain, secure){
	var arr = getcookie_array(name);
	arr[key] = value;
	var arr_json = '{';
	separator = '';
	for (i in arr){
		arr_json = arr_json + separator + '"' + i + '":"' + arr[i] + '"';
		separator = ', ';
	}
	arr_json = arr_json + '}';
	setcookie(name, arr_json, expires, path, domain, secure);
}
function removecookie_array(name, key, expires, path, domain, secure){
	var arr = getcookie_array(name);
	if (isset(arr[key])){
		delete arr[key];
		var arr_json = '{';
		separator = '';
		for (i in arr){
			arr_json = arr_json + separator + '"' + i + '":"' + arr[i] + '"';
			separator = ', ';
		}
		arr_json = arr_json + '}';
		setcookie(name, arr_json, expires, path, domain, secure);
	}
}
function setrawcookie(name, value, expires, path, domain, secure) {
	
	if (expires instanceof Date) {
		expires = expires.toGMTString();
	} else if(typeof(expires) == 'number') {
		expires = (new Date(+(new Date()) + expires * 1e3)).toGMTString();
	}
	
	var r = [name + "=" + value], s={}, i='';
	s = {expires: expires, path: path, domain: domain};
	for(i in s){
	  s[i] && r.push(i + "=" + s[i]);
	}
	
	return secure && r.push("secure"), this.window.document.cookie = r.join(";"), true;
}
function redirect(link){
	window.location.href = link;
}
function str_replace(search, replace, subject, count) {
	 
	var i = 0, j = 0, temp = '', repl = '', sl = 0, fl = 0,
			f = [].concat(search),
			r = [].concat(replace),
			s = subject,
			ra = r instanceof Array, sa = s instanceof Array;
	s = [].concat(s);
	if (count) {
		this.window[count] = 0;
	}
 
	for (i=0, sl=s.length; i < sl; i++) {
		if (s[i] === '') {
			continue;
		}
		for (j=0, fl=f.length; j < fl; j++) {
			temp = s[i]+'';
			repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
			s[i] = (temp).split(f[j]).join(repl);
			if (count && s[i] !== temp) {
				this.window[count] += (temp.length-s[i].length)/f[j].length;}
		}
	}
	return sa ? s : s[0];
}
function strpos( haystack, needle, offset){ 
	var i = (haystack+'').indexOf(needle, (offset ? offset : 0));
	return i === -1 ? false : i;
}
function replace_commas(obj){
	var $this = $(obj);
	var t = $this.val();
	$this.val(t.replace(/,/g,'.'));
}
function is_array(input){
	return typeof(input)=='object'&&(input instanceof Array);
}
var active_forms = new Array();
function select_form(formId, linkIdName, formClassName, formsCount, formNo, values, preAction, postAction, methodHideShow) {
	if (active_forms[formId] != formNo){
		if (preAction) {
			preAction(formId, linkIdName, formClassName, formsCount, formNo, values, methodHideShow);
		}
		var i;
		var $field; 
		for (i=1;i<=formsCount;i++){
			$field = $("#".formId).find('.'+formClassName+i);
			switch(methodHideShow) {
				case 'slide':
					$field.slideUp("slow");
					break;
				case 'display':
					$field.css("display", "none");
					break;
				default:
					$field.hide("slow");
			}
			$("#"+linkIdName+i).removeClass('active');
		}
		$field = $("#".formId).find('.'+formClassName+formNo);
		switch(methodHideShow) {
			case 'slide':
				$field.slideDown("slow");
				break;
			case 'display':
				$field.css("display", "block");
				break;
			default:
				$field.show("slow");
		}
		$("#"+linkIdName+formNo).addClass('active');
		if (values) {
			for ( var i in values ){
				$('#'+i).val(values[i]);
			}
		}
		if (postAction) {
			postAction(formId, linkIdName, formClassName, formsCount, formNo, values, methodHideShow);
		}
		active_forms[formId] = formNo;
	}
}

function deserialize2array(str){
	return eval('({"'+str.replace(/=/g,'":"').replace(/&/g,'","')+'"})');
}

function parse_url (str, component) {
	var  o   = {
		strictMode: false,
		key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
		q: {
			name:   "queryKey",
			parser: /(?:^|&)([^&=]*)=?([^&]*)/g
		},
		parser: {
			strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
			loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/\/?)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/ // Added one optional slash to post-protocol to catch file:/// (should restrict this)
		}
	};
	
	var m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
	uri = {},
	i   = 14;
	while (i--) {uri[o.key[i]] = m[i] || "";}
	switch (component) {
		case 'PHP_URL_SCHEME':
			return uri.protocol;
		case 'PHP_URL_HOST':
			return uri.host;
		case 'PHP_URL_PORT':
			return uri.port;
		case 'PHP_URL_USER':
			return uri.user;
		case 'PHP_URL_PASS':
			return uri.password;
		case 'PHP_URL_PATH':
			return uri.path;
		case 'PHP_URL_QUERY':
			return uri.query;
		case 'PHP_URL_FRAGMENT':
			return uri.anchor;
		default:
			var retArr = {};
			//if (uri.protocol !== '')
				retArr.scheme=uri.protocol;
			//if (uri.host !== '')
				retArr.host=uri.host;
			//if (uri.port !== '')
				retArr.port=uri.port;
			//if (uri.user !== '')
				retArr.user=uri.user;
			//if (uri.password !== '')
				retArr.pass=uri.password;
			//if (uri.path !== '')
				retArr.path=uri.path;
			//if (uri.query !== '')
				retArr.query=uri.query;
			//if (uri.anchor !== '')
				retArr.fragment=uri.anchor;
			return retArr;
	}
}