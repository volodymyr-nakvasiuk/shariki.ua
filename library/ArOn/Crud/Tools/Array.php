<?php
/**
 * Класс для работы с масивами
 *
 */
class ArOn_Crud_Tools_Array {
	/**
	 * Функция переконфертирования масива в асоциативный.
	 *
	 * @param array $array входной масив
	 * @param string $display поле-результат для асоциативного масива
	 * @param string $key поле-ключ для асоциативного масива
	 * @param string $empty тект для нулевого ключа. (для комбо например "виберите...")
	 * @return array
	 */
	static function arrayToAssoc($array, $display = 'name', $key = 'id', $empty = '') {
		$arrReturn = array ();
		if ($empty) {
			$arrReturn [0] = $empty;
		}

		foreach ( $array as $k => $v ) {
			if (is_array ( $v ) && ! empty ( $v [$key] )) {
				if ($display && isset ( $v [$display] )) {
					$arrReturn [$v [$key]] = $v [$display];
				} else {
					$arrReturn [$v [$key]] = $v [$key];
				}
					
			} else {
				//$arrReturn[$k]=$v;
			}
		}

		return $arrReturn;
	}

	static function assocToLinearArray($array, $key = 'id') {
		$res = array ();
		foreach ( $array as $v ) {
			$res [] = $v [$key];
		}
		return $res;
	}

	/**
	 * Функция переконфертирования многомерного асоциативного масива в одномерныйй асоциативный.
	 *
	 * @param array $array входной масив
	 * @param string $prefix префикс для ключей
	 * @return array
	 */
	static function arrayToLinearArray($array, $prefix = "") {
		$res = array ();
		foreach ( $array as $k => $v ) {
			if (is_array ( $v )) {
				$a = self::arrayToLinearArray ( $v, $prefix );
				$res = array_merge ( $res, $a );
			} else {
				$res [$prefix . $k] = $v;
			}
		}
		return $res;
	}

	static function accessArrayToLinearArray($array, $key = "key", $name = "name") {
		//print_r($array);
		$res = array ();
		foreach ( $array as $v ) {
			if (! empty ( $v [$key] ) && ! empty ( $v [$name] )) {
				$res [$v [$key]] = $v [$name];
			}
			if (! empty ( $v ['children'] ) && is_array ( $v ['children'] )) {
				$a = self::accessArrayToLinearArray ( $v ['children'], $key, $name );
				$res = array_merge ( $res, $a );
			}
		}
		return $res;
	}

	static function setAccessArrayFromTree($post, $array, $key = "key") {
		$res = array ();
		foreach ( $array as $v ) {
				
			if (! empty ( $post [$v [$key]] ) || ! empty ( $v ['inherit'] )) {
				if (! empty ( $v [$key] )) {
					$res [$v [$key]] = 1;
				}
				if (! empty ( $v ['children'] ) && is_array ( $v ['children'] )) {
						
					$a = self::setAccessArrayFromTree ( $post, $v ['children'], $key );
					$res = array_merge ( $res, $a );
				}
			}
		}
		return $res;
	}

	static function filterAccessTree($tree, $access) {
		$res = array ();
		foreach ( $tree as $k => $v ) {
			if (! isset ( $v ['access'] ) || ! empty ( $access [$v ['access']] )) {
				$p = $v;

				if (isset ( $p ['children'] )) {
					unset ( $p ['children'] );
				}

				if (! empty ( $v ['children'] ) && is_array ( $v ['children'] )) {
						
					$a = self::filterAccessTree ( $v ['children'], $access );
					if (! empty ( $a )) {
						$p ['children'] = $a;
					}
				}
				$res [] = $p;
			}
		}
		return $res;
	}

	static function filterInheritedAccessTree($tree) {
		$res = array ();
		foreach ( $tree as $k => $v ) {
			if (empty ( $v ['inherit'] )) {
				$p = $v;
				if (isset ( $p ['children'] )) {
					unset ( $p ['children'] );
				}
				if (! empty ( $v ['children'] ) && is_array ( $v ['children'] )) {
						
					$a = self::filterInheritedAccessTree ( $v ['children'] );
					if (! empty ( $a )) {
						$p ['children'] = $a;
					}
				}
				$res [] = $p;
			}
		}
		return $res;
	}

	static function isMultiArray($multiarray) {
		if (is_array ( $multiarray )) { // confirms array
			foreach ( $multiarray as $array ) { // goes one level deeper
				if (is_array ( $array )) { // is subarray an array
					return true; // return will stop function
				} // end 2nd check
			} // end loop
		} // end 1st check
		return false; // not a multiarray if this far
	}


	static function arraySearchRecursive( $needle, &$haystack, $strict=false, $path=array() , $key_search = false, $new_value = false){
		if( !is_array($haystack) ) {
			return false;
		}
		foreach( $haystack as $key => $val ) {
			if( is_array($val) && $subPath = self::arraySearchRecursive($needle, $haystack[$key], $strict, $path, $key_search, $new_value) ) {
				if($key_search)
				return $subPath;
				//elseif($key_search && $new_value)
				//return $haystack [$key] = $subPath;
				else $path = array_merge($path, array($key), $subPath);
				return $path;
			} else{
				if($key_search == true) {
					$value = $val;
					$val = $key;
				}
				if( (!$strict && $val === $needle) || ($strict && $val === $needle) ) {
					$path[] = $key;
					if($key_search && $new_value)
					$haystack[$needle] = $new_value;
					return ($key_search) ? $haystack[$needle] : $path;
				}
			}
		}
		return false;
	}
	
	static function joinr($join, $value, $lvl=0)
    {
        if (!is_array($join)) return joinr(array($join), $value, $lvl);
        $res = array();
        if (is_array($value)&&sizeof($value)&&is_array(current($value))) { // Is value are array of sub-arrays?
            foreach($value as $val)
                $res[] = joinr($join, $val, $lvl+1);
        }
        elseif(is_array($value)) {
            $res = $value;
        }
        else $res[] = $value;
        return join(isset($join[$lvl])?$join[$lvl]:"", $res);
    }
	
    static function mt_implode($char,$array,$fix='',$addslashes=false)
	{
	    $lem = array_keys($array);
	    $char = htmlentities($char);
	    for($i=0;$i<sizeof($lem);$i++) {
	      if($addslashes){
	        $str .= $fix.(($i == sizeof($lem)-1) ? addslashes($array[$lem[$i]]).$fix : addslashes($array[$lem[$i]]).$fix.$char);
	      }else{
	        $str .= $fix.(($i == sizeof($lem)-1) ? $array[$lem[$i]].$fix : $array[$lem[$i]].$fix.$char);
	      }
	    }
	    return $str;
	}
    
    static function getAllValuesVariantsFromArrays($arrays, $sub_variants = false, $inverted = false){
    	$results = array();
    	if(count($arrays) == 1) return $arrays;
    	if(count($arrays) == 2) {
    		if ($sub_variants){    			
	    		$result = self::getValuesVariantsFrom2Array($arrays[0],false);
	    		$results = array_merge($results,$result);
    			$result = self::getValuesVariantsFrom2Array($arrays[1],false);
    			$results = array_merge($results,$result);
    		}
    		$result = self::getValuesVariantsFrom2Array($arrays[0],$arrays[1]);
    		$results = array_merge($results,$result);
    		return $results;
    	}
    	foreach($arrays as $x => $array1){
    		$d_array = array();
	    	foreach($arrays as $y => $array2){
	    		if($inverted === false && $y < $x)
	    			continue;
	    		if($x == $y){ 
	    			if ($sub_variants)
	    				$array2 = false;
	    			else continue;
	    		}
	    		if($sub_variants === false && ($y-1) > $x)
	    			continue;
	    		$result = self::getValuesVariantsFrom2Array($array1,$array2);
	    		//$d_array[] = array_merge($d_array,$result);
	    		$d_array[] = $result;
	    	}
	    	if ($sub_variants)
	    		foreach($d_array as $array){
	    			$results = array_merge($results,$array);
	    		}
	    	$t_array = ($sub_variants) ? $d_array[1] : $d_array[0];
	    	for($i=$x+2;$i<count($arrays);$i++){
	    		$t_array = self::getValuesVariantsFrom2Array($t_array,$arrays[$i]);
	    		if (!$sub_variants && ($i+1 < count($arrays)))
	    			continue;
	    		$results = array_merge($results,$t_array);
	    	}
	    	if($sub_variants === false) return $results;
    	}
    	return $results;
    }
    
    static function getValuesVariantsFrom2Array($array1,$array2 = false){
    	$results = array();
    	foreach($array1 as $value1){
    		if(!is_array($value1)){
    			$value1 = array($value1);
    		}
    		if($array2 === false){
    			$results[] = $value1;
    			continue;
    		}
    		foreach($array2 as $value2){
	    		if(!is_array($value2)){
	    			$value2 = array($value2);
	    		}
	    		$results[] = array_merge($value1,$value2);
    		}
    	}
    	return $results;
    }
}