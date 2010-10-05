<?php
class ArOn_Crud_Grid_Column_Image extends ArOn_Crud_Grid_Column_Default {

	public $width = '100';
	public $height = false;
	public $alt = false;
	public $baseDir = false;
	public $template;

	function __construct($title, $name = null, $isSort = true, $hidden = false, $width = false, $template = false, $render_function = false, $info_block = false, $height = false, $baseDir = false) {

		parent::__construct ($title, $name, $isSort, $hidden, $width, $render_function, $info_block);
		$this->width = $width;
		$this->height = $height;
		$this->template = $template;
		if (!is_array($this->template)) $this->template = array($this->template);
		$this->baseDir = $baseDir;
	}

	public function render(array &$row) {
		foreach ($this->template as $template){
			while (($start = strpos($template,'{')) !== false && ($end = strpos($template,'}',$start)) !== false){
				$key = substr($template,$start+1,$end-$start-1);
				$tvalue = (key_exists($key, $row)) ? $row [$key] : "";
				$template = str_replace ( "{".$key."}", $tvalue, $template );
			}
			if ($this->baseDir){
				if(is_file($this->baseDir.$template)) break;
			}
			else {
				break;
			}
		}
		$xhtml = $this->createImage($template);
		return $xhtml;
		//return $value;
	}

	public function createImage($image)
	{
		$src = ' src="' . $image . '"';

		$width = 'width: ' . $this->width .'px;';
		if($this->height) $height = 'height: ' . $this->height .'px;'; else $height = '';
		if($this->gridTitleField) $title = 'title="' . $this->gridTitleField . '"'; else $title = '';
		if($this->alt) $title = 'title="' . $this->alt . '"'; else $atl = '';

		$endTag = ' />';


		// build the element
		$xhtml = '<img '
		. ' id="' . $this->row_id . '"'
		. $src
		. ' style="' . $width . $height . '"'
		. $title
		. $atl
		. $endTag;

		return $xhtml;
	}
}