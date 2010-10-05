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
function setcookie(name, value, expires, path, domain, secure) {
	    return this.setrawcookie(name, encodeURIComponent(value), expires, path, domain, secure);
}
function getcookie(c_name)
{
if (document.cookie.length>0)
  {
  c_start=document.cookie.indexOf(c_name + "=");
  if (c_start!=-1)
    {
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