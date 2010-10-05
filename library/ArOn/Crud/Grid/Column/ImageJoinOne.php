<?php

class ArOn_Crud_Grid_Column_ImageJoinOne extends ArOn_Crud_Grid_Column_JoinOne {
	
	protected $_imgPath;
	protected $_parserImg;
	public $width;
	public $height;
	
	function __construct($title, $rules = null, $tableField = null, $tableFields = null, $hidden = false, $width = 85, $height=false, $img_path='', $parser_img = false) {
		parent::__construct ($title, $rules, $tableField, $tableFields, $hidden, $width);
		$this->_imgPath = $img_path;
		$this->_parserImg = $parser_img;
		$this->width = $width;
		$this->height = $height;
	}
	
	public function render($row) {
		$imgPath = $this->_imgPath;
		while (($start = strpos($imgPath,'{')) !== false && ($end = strpos($imgPath,'}',$start)) !== false){
			$key = substr($imgPath,$start+1,$end-$start-1);
			$value = (key_exists($key, $row)) ? $row [$key] : "";
			$imgPath = str_replace ( "{".$key."}", $value, $imgPath );
		}
		
		if (!is_file(DOCUMENT_ROOT.$imgPath)){
			$imgPath = '/img/nophoto_m.gif';
			
			if ($this->_parserImg){
				$parserImg = $this->_parserImg;
				while (($start = strpos($parserImg,'{')) !== false && ($end = strpos($parserImg,'}',$start)) !== false){
					$key = substr($parserImg,$start+1,$end-$start-1);
					$value = (key_exists($key, $row)) ? $row [$key] : "";
					$parserImg = str_replace ( "{".$key."}", $value, $parserImg );
				}
				
				$imgPath = Tools_Bmsimages::getSmallImage(0, $parserImg);
			}
		}

		return $this->createImage($imgPath);
	}
	
	public function createImage($image)	{
		$src = ' src="' . $image . '"';

		$width = 'width: ' . $this->width .'px;';
		if($this->height) $height = 'height: ' . $this->height .'px;'; else $height = '';

		$endTag = ' />';


		// build the element
		$xhtml = '<img '
		. $src
		. ' style="' . $width . $height . 'margin: auto;"'
		. $endTag;

		return $xhtml;
	}

}