<?php
class ArOn_Crud_Tools_String {

	static function realStripTags($i_html, $i_allowedtags = array(), $i_trimtext = FALSE) {
	  if (!is_array($i_allowedtags))
	    $i_allowedtags = !empty($i_allowedtags) ? array($i_allowedtags) : array();
	  $tags = implode('|', $i_allowedtags);
	
	  if (empty($tags))
	    $tags = '[a-z]+';
	
	  preg_match_all('@</?\s*(' . $tags . ')(\s+[a-z_]+=(\'[^\']+\'|"[^"]+"))*\s*/?>@i', $i_html, $matches);
	
	  $full_tags = $matches[0];
	  $tag_names = $matches[1];
	
	  foreach ($full_tags as $i => $full_tag) {
	    if (!in_array($tag_names[$i], $i_allowedtags))
	      if ($i_trimtext)
	        unset($full_tags[$i]);
	      else
	        $i_html = str_replace($full_tag, '', $i_html);
	  }
	
	  return $i_trimtext ? implode('', $full_tags) : $i_html;
	}
	
	static function convToUtf8($str){		
		if( ($encoding = self::detect_encoding($str))!="UTF-8" ){
			return  iconv($encoding,"UTF-8",$str);		
		}		
		else
			return $str;
	}
	
	static function convToWin($str){		
		if( ($encoding = self::detect_encoding($str))!="windows-1251" ){
			return  iconv($encoding,"windows-1251",$str);		
		}		
		else
			return $str;
	}
	
	static function detect_encoding($string) { 
	  static $list = array('windows-1251','UTF-8', 'ISO-8859-1', 'GBK');
	 
	  foreach ($list as $item) {
	    $sample = iconv($item, $item, $string);
	    if (md5($sample) == md5($string))
	      return $item;
	  }
	  return null;
	}
	
	static function quote($value, $type = null) {

		if (is_array ( $value )) {
			foreach ( $value as &$val ) {
				$val = self::quote ( $val, $type );
			}
			return implode ( ', ', $value );
		}

		if ($type !== null) {
			switch ($type) {
				case 0 : // 32-bit integer
					return ( string ) intval ( $value );
					break;
				case 1 : // 64-bit integer
					// ANSI SQL-style hex literals (e.g. x'[\dA-F]+')
					// are not supported here, because these are string
					// literals, not numeric literals.
					if (preg_match ( '/^(
                          [+-]?                  # optional sign
                          (?:
                            0[Xx][\da-fA-F]+     # ODBC-style hexadecimal
                            |\d+                 # decimal or octal, or MySQL ZEROFILL decimal
                            (?:[eE][+-]?\d+)?    # optional exponent on decimals or octals
                          )
                        )/x', ( string ) $value, $matches )) {
					return $matches [1];
                        }
                        break;
				case 2 : // float or decimal
					return ( string ) floatval ( $value );
					break;
			}
			return '0';
		}

		if (is_int ( $value ) || is_float ( $value )) {
			return $value;
		}
		return "'" . addcslashes ( $value, "\000\n\r\\'\"\032" ) . "'";

	}

	public function ConvertString($str, $length = 256) {

		$Letters = array ();
		$Letters ["а"] = "a";
		$Letters ["б"] = "b";
		$Letters ["в"] = "v";
		$Letters ["г"] = "g";
		$Letters ["д"] = "d";
		$Letters ["е"] = "e";
		$Letters ["ё"] = "e";
		$Letters ["ж"] = "zh";
		$Letters ["з"] = "z";
		$Letters ["и"] = "i";
		$Letters ["й"] = "y";
		$Letters ["к"] = "k";
		$Letters ["л"] = "l";
		$Letters ["м"] = "m";
		$Letters ["н"] = "n";
		$Letters ["о"] = "o";
		$Letters ["п"] = "p";
		$Letters ["р"] = "r";
		$Letters ["с"] = "s";
		$Letters ["т"] = "t";
		$Letters ["у"] = "u";
		$Letters ["ф"] = "f";
		$Letters ["х"] = "h";
		$Letters ["ц"] = "c";
		$Letters ["ч"] = "ch";
		$Letters ["ш"] = "sh";
		$Letters ["щ"] = "sch";
		$Letters ["ъ"] = "";
		$Letters ["ы"] = "y";
		$Letters ["ь"] = "";
		$Letters ["э"] = "e";
		$Letters ["ю"] = "yu";
		$Letters ["я"] = "ya";
		$Letters ["А"] = "a";
		$Letters ["Б"] = "b";
		$Letters ["В"] = "v";
		$Letters ["Г"] = "g";
		$Letters ["Д"] = "d";
		$Letters ["Е"] = "e";
		$Letters ["Ё"] = "e";
		$Letters ["Ж"] = "zh";
		$Letters ["З"] = "z";
		$Letters ["И"] = "i";
		$Letters ["Й"] = "y";
		$Letters ["К"] = "k";
		$Letters ["Л"] = "l";
		$Letters ["М"] = "m";
		$Letters ["Н"] = "n";
		$Letters ["О"] = "o";
		$Letters ["П"] = "p";
		$Letters ["Р"] = "r";
		$Letters ["С"] = "s";
		$Letters ["Т"] = "t";
		$Letters ["У"] = "u";
		$Letters ["Ф"] = "f";
		$Letters ["Х"] = "h";
		$Letters ["Ц"] = "c";
		$Letters ["Ч"] = "ch";
		$Letters ["Ш"] = "sh";
		$Letters ["Щ"] = "sch";
		$Letters ["Ъ"] = "";
		$Letters ["Ы"] = "y";
		$Letters ["Ь"] = "";
		$Letters ["Э"] = "e";
		$Letters ["Ю"] = "yu";
		$Letters ["Я"] = "ya";
		//$Letters[" "] = "_";
		$Letters [" "] = "-";

		/* знаки припенания */
		$Letters [","] = "";
		$Letters [";"] = "";
		$Letters [":"] = "";
		$Letters ["."] = "";
		$Letters ["!"] = "";
		$Letters ["?"] = "";
		//$Letters["-"] = "_";


		/* спецсимволы */
		$Letters ["`"] = "";
		$Letters ["\""] = "";
		$Letters ["'"] = "";
		$Letters ["%"] = "";
		$Letters ["&"] = "";
		$Letters ["$"] = "";
		$Letters ["#"] = "";
		$Letters ["/"] = "";
		$Letters ["\\"] = "";

		/* немецкий */
		$Letters ["a"] = "a";
		$Letters ["o"] = "o";
		$Letters ["?"] = "b";
		$Letters ["u"] = "u";

		$new_str = "";
		//$str = @mb_strtolower($str,"UTF8");
		$str = @strtolower ( $str );

		foreach ( $Letters as $key => $value ) {
			$str = str_replace ( $key, $value, $str );
		}
		$new_str = substr ( $str, 0, $length );
		return $new_str;
	}

	public function rus2translit($string) {
		$converter = array ('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ь' => '\'', 'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '', 'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya' );
		return strtr ( $string, $converter );
	}
	
	public function translit2rus($st) {
	  $st = strtr($st, array(
	    "ё"=>'yo',    "х"=>'h',  "ц"=>'ts',  "ч"=>'ch', "ш"=>'sh',  
	    "o"=>'shch',  "ю"=>'yu', "я"=>'ya',
	    "Ё"=>'Yo',    "Х"=>'H',  "Ц"=>'Ts',  "Ч"=>'Ch', "Ш"=>'Sh',
	    "Щ"=>'Shch',  "Ю"=>'Yu', "Я"=>'Ya',
	  ));
	  $st = strtr($st, 
	    "abvgdegziyklmnoprstufieABVGDEGZIYKLMNOPRSTUFIE",
	    "абвгдежзийклмнопрстуфыАБВГДЕЖЗИЙКЛМНОПРСТУФЫЭ"
	  );
	  return $st;
	} 

}