<?php
class Tools_Bmsimages {
	
	static function getImageSize($size){
		$Q = array(
				'small' => array('folder' => 'small' , 'file' => 's', 'price_nf'=>'nophoto_n.gif', 'catalog_nf'=>'nophoto_e.gif'),
				'middle' => array('folder' => 'middle' , 'file' => 'm', 'price_nf'=>'nophoto_b.gif', 'catalog_nf'=>'nophoto_q.gif'),
				'big' => array('folder' => 'big' , 'file' => 'b', 'price_nf'=>'nophoto_c.gif', 'catalog_nf'=>'nophoto_p.gif')
				);
		return (array_key_exists($size,$Q)) ? $Q[$size] : false;
	}
	
	static $imgSernerNo = 1;
	static $imgSernerMin = 1;
	static $imgSernerMax = 3;
	static function getImgServer(){
		self::$imgSernerNo++;
		if (self::$imgSernerNo>self::$imgSernerMax) self::$imgSernerNo = self::$imgSernerMin;
		return str_replace('http://', 'http://img'.self::$imgSernerNo.'.', HOST_NAME);
	}
	
	static $imgNewsSernerNo = 1;
	static $imgNewsSernerMin = 1;
	static $imgNewsSernerMax = 3;
	static function getImgNewsServer(){
		self::$imgNewsSernerNo++;
		if (self::$imgNewsSernerNo>self::$imgNewsSernerMax) self::$imgNewsSernerNo = self::$imgNewsSernerMin;
		return str_replace('http://', 'http://img'.self::$imgNewsSernerNo.'.', NEWS_HOST_NAME);
	}
}