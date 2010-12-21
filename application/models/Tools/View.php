<?php
class Tools_View{
	
	static function parse_str($urlParamStr){
		$paramArr = explode('&', $urlParamStr);
		$return = array();
		foreach ($paramArr as $param) {
			$tmp = explode('=', $param);
			if ($tmp[0])
				$return[$tmp[0]] = $tmp[1];
		}
		return $return;
	}
	
	/*
	 * function setUrlParam sets parameters values in URL
	 * $url - URL to set parameters in
	 * $paramName - array of parameters names
	 *              if one parameter to set $paramName can be string
	 * $paramValue - array of parameters values
	 *               if one parameter to set $paramValue can be string
	 * $paramName and $paramValue must be same size arrays!
	 * 
	 * if not set $paramValue - $paramName must be
	 * array of names and values: array(name1=>value1, name2=>value2)
	 */
	static function setUrlParam($url, $paramName, $paramValue = null, $urlDecode = false) {
		if (!is_array($paramName)){
			$paramName = array($paramName);
		}
		if ($paramValue !== null){
			if (!is_array($paramValue)){
				$paramValue = array($paramValue);
			}
			if (($paramsArray = array_combine($paramName, $paramValue)) === false){
				return $url;
			}
		}
		else {
			$paramsArray = $paramName;
		}
		$paramsArray['car'] = isset($paramsArray['car'])?$paramsArray['car']:null;
		$parse_url = parse_url($url);
		$url = $parse_url['scheme'].'://'.$parse_url['host'].$parse_url['path'];
		$parse_str = self::parse_str($parse_url['query']);
		$parse_str = array_merge($parse_str, $paramsArray);
		$query = '';
		if ($query = http_build_query($parse_str)){
			$url .= '?'.$query;
		}
		if ($urlDecode) $url = urldecode($url);
		return $url;
	}
	
	static function clearUrlParam($url, $clearArray) {
		$parse_url = parse_url($url);
		$url = $parse_url['scheme'].'://'.$parse_url['host'].$parse_url['path'];
		$parse_str = self::parse_str($parse_url['query']);
		foreach ($clearArray as $paramName){
			unset($parse_str[$paramName]);
		}
		$query = '';
		if ($query = http_build_query($parse_str)){
			$url .= '?'.$query;
		}
		return $url;
	}
	
	static function getCanonicalLink($url, $params = null){
		if(!$params || !is_array($params)) return $url;
		
		$parse_url = parse_url($url);
		$url = $parse_url['scheme'].'://'.$parse_url['host'].$parse_url['path'];
		$parse_str = self::parse_str($parse_url['query']);
		foreach($params as $paramName => $config){
			if ($config !== false){
				if (!is_array($config)) $config = array('default'=>$config);
				if (isset($config['allowed']) && !is_array($config['allowed'])) $config['allowed'] = array($config['allowed']);
				
				if (
					(
						isset($config['default']) &&
						$parse_str[$paramName]==$config['default']
					)
					||
					(
						isset($config['allowed']) &&
						!in_array($parse_str[$paramName], $config['allowed'])
					)
				) unset($parse_str[$paramName]);
			}
			else {
				unset($parse_str[$paramName]);
			}
		}
		$query = '';
		if ($query = http_build_query($parse_str)){
			$url .= '?'.$query;
		}
		return $url;
	}
	
	static function getPages($url, $arrayPages, $pageParamName) {
		$str = '';
		if ($arrayPages['first']){
			$link  = self::setUrlParam($url, $pageParamName, $arrayPages['first']);
			$first = '<span><a href="'.$link.'"><img src="'.STATIC_HOST_NAME.'/img/page_arrow_first.gif"/></a></span>';
		}
		else {
			$first = '<span><img src="'.STATIC_HOST_NAME.'/img/page_arrow_first.gif"/></span>';
		}
		unset($arrayPages['first']);
		if ($arrayPages['prev']){
			$link   = self::setUrlParam($url, $pageParamName, $arrayPages['prev']);
			$prev   = '<span><a href="'.$link.'"><img src="'.STATIC_HOST_NAME.'/img/page_arrow_prev.gif"/></a></span>';
			$prev_w = '<span><a href="'.$link.'">назад</a></span>';
		}
		else {
			$prev   = '<span><img src="'.STATIC_HOST_NAME.'/img/page_arrow_prev.gif"/></span>';
			$prev_w = '<span>назад</span>';
		}
		unset($arrayPages['prev']);
		if ($arrayPages['next']){
			$link   = self::setUrlParam($url, $pageParamName, $arrayPages['next']);
			$next   = '<span><a href="'.$link.'"><img src="'.STATIC_HOST_NAME.'/img/page_arrow_next.gif"/></a></span>';
			$next_w = '<span><a href="'.$link.'">вперед</a></span>';
		}
		else {
			$next   = '<span><img src="'.STATIC_HOST_NAME.'/img/page_arrow_next.gif"/></span>';
			$next_w = '<span>вперед</span>';
		}
		unset($arrayPages['next']);
		if ($arrayPages['last']){
			$link = self::setUrlParam($url, $pageParamName, $arrayPages['last']);
			$last = '<span><a href="'.$link.'"><img src="'.STATIC_HOST_NAME.'/img/page_arrow_last.gif"/></a></span>';
		}
		else {
			$last = '<span><img src="'.STATIC_HOST_NAME.'/img/page_arrow_last.gif"/></span>';
		}
		unset($arrayPages['last']);
		$str .= $prev_w;//.$first.$prev;
		foreach($arrayPages as $page=>$status){
			if ($status == 'now'){
				$str .= '<span class="page_now">'.$page.'</span>';
			}
			else {
				$link = self::setUrlParam($url, $pageParamName, $page);
				$str .= '<span class="page_link"><a href="'.$link.'">'.$page.'</a></span>';
			} 
		}
		$str .= //$next.$last.
				$next_w;
		return($str);
	}
}