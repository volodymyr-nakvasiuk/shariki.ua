<?php
class ArOn_Crud_Tools_Random{

	//const ValidChars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	//const ValidChars = "0123456789abcdfghjkmnpqrstvwxyz";
	static $ValidChars = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	//const ValidChars = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";

	static function generate($length=6){

		list($usec, $sec) = explode(' ', microtime());
		srand((float) $sec + ((float) $usec * 100000));

		$string = "";
		$strlen = strlen(self::$ValidChars)-1;

		for ($i=0;$i<$length;$i++) {
			$string .= self::$ValidChars{rand(0, $strlen)};
		}

		return $string;

	}

}
?>

