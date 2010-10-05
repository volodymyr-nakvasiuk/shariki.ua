<?php
class ArOn_Crud_Tools_Numeric{
	
	
	static function str2float($string, $concat = true) {
	    $length = strlen($string);   
	    for ($i = 0, $int = '', $concat_flag = true; $i < $length; $i++) {
	        if (is_numeric($string[$i]) && $concat_flag) {
	            $int .= $string[$i];
	        } elseif(!$concat && $concat_flag && strlen($int) > 0) {
	            $concat_flag = false;
	        }       
	    }
	   
	    return (float) $int;
	}
	
	static $MATRIX = array (
							array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 0 ),
							array( 2, 3, 4, 5, 6, 7, 8, 9, 0, 1 ),
							array( 3, 4, 5, 6, 7, 8, 9, 0, 1, 2 ),
							array( 4, 5, 6, 7, 8, 9, 0, 1, 2, 3 ),
							array( 5, 6, 7, 8, 9, 0, 1, 2, 3, 4 ),
							array( 6, 7, 8, 9, 0, 1, 2, 3, 4, 5 ),
							array( 7, 8, 9, 0, 1, 2, 3, 4, 5, 6 ),
							array( 8, 9, 0, 1, 2, 3, 4, 5, 6, 7 ),
							array( 9, 0, 1, 2, 3, 4, 5, 6, 7, 8 ),
							array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 )
						);
	
	const SpecLevel = 7;
						
	static function encode($string){
		$matrix = self::$MATRIX;
		$level = self::SpecLevel;
		$workMatrix = $matrix [ $level ];
		$result = "";
		$string = strval($string);
		for($i=0; $i < strlen($string); $i++){
			$key = intval ( $string [$i] );
			$result .= $workMatrix [ $key ];
		}
		return $result;
	}
	
	static function decode($string){
		$matrix = self::$MATRIX;
		$level = self::SpecLevel;
		$workMatrix = $matrix [ $level ];
		$result = "";
		$string = strval($string);
		for($i=0;$i<strlen($string);$i++){
			$val = intval ( $string [$i] );
			$result .= array_search( $val, $workMatrix);			
		}

		return $result;

	}
	
}
