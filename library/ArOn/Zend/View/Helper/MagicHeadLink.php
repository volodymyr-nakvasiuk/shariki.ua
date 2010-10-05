<?php
/**
 * @license Public domain
 */
class ArOn_Zend_View_Helper_MagicHeadLink extends Zend_View_Helper_HeadLink
{
	private static $cacheDir;
	private static $combine = 1;
	private static $compress = 1;
	private static $symlinks = array();
	private static $compileImages = array();

	private $_cache = array();
	
	static public function setConfig($compress = 1, $symlinks = array(), $compileImages = false)
	{
		self::$symlinks = $symlinks;
		self::$compress = $compress;
		self::$compileImages = $compileImages;
	}
	
	protected function setup(){
		self::$cacheDir = $this->view->getMinifyType('CSS');
		self::$combine = self::$cacheDir?1:0;
	}
	
	public function magicHeadLink()
	{
		$this->setup();
		if (self::$combine) {
			return $this->toString();
		} else {
			return $this->view->headLink();
		}
	}
	
	public function itemToString(stdClass $item)
	{
		$attributes = (array) $item;
		if ($attributes['href'] && strpos($attributes['href'], 'http://')===false) $attributes['href'] = STATIC_HOST_NAME.$attributes['href'];
		$link = '<link ';

		foreach ($this->_itemKeys as $itemKey) {
			if (isset($attributes[$itemKey])) {
				if(is_array($attributes[$itemKey])) {
					foreach($attributes[$itemKey] as $key => $value) {
						$link .= sprintf('%s="%s" ', $key, ($this->_autoEscape) ? $this->_escape($value) : $value);
					}
				} else {
					$link .= sprintf('%s="%s" ', $itemKey, ($this->_autoEscape) ? $this->_escape($attributes[$itemKey]) : $attributes[$itemKey]);
				}
			}
		}

		if ($this->view instanceof Zend_View_Abstract) {
			$link .= ($this->view->doctype()->isXhtml()) ? '/>' : '>';
		} else {
			$link .= '/>';
		}

		if (($link == '<link />') || ($link == '<link >')) {
			return '';
		}

		if (isset($attributes['conditionalStylesheet'])
			&& !empty($attributes['conditionalStylesheet'])
			&& is_string($attributes['conditionalStylesheet']))
		{
			$link = '<!--[if ' . $attributes['conditionalStylesheet'] . ']>'.($attributes['conditionalStylesheet']=='!IE'?'<!-->'.$link.'<!--':$link).'<![endif]-->';
		}

		return $link;
	}

	public function isCachable($item)
	{
		$attributes = (array) $item;
		if (isset($attributes['conditionalStylesheet'])
			&& !empty($attributes['conditionalStylesheet'])
			&& is_string($attributes['conditionalStylesheet']))
		{
			return false;
		}
		if ($attributes['non-cache']) {			
			return false;
		}
		if (!isset($attributes['href']) || !is_readable($_SERVER['DOCUMENT_ROOT'] . $attributes['href'])) {			
			return false;
		}
		$info = pathinfo($_SERVER['DOCUMENT_ROOT'] . $attributes['href']);
		if($info['extension'] == 'ico'){
			return false;
		}
		return true;
	}
	
	public function cache($item)
	{
		$attributes = (array) $item;
		$filePath = DOCUMENT_ROOT . $attributes['href'];
   		$key = md5($filePath);
		if(!isset($this->_cache[$key]))
			$this->_cache[$key] = array(
				'filepath' => $filePath,
				'mtime' => filemtime($filePath)
			);
		
	}
	
	public function toString($indent = null)
	{
		$headLink = $this->view->headLink();
		
		$indent = (null !== $indent)
				? $headLink->getWhitespace($indent)
				: $headLink->getIndent();

		$items = array();
		$headLink->getContainer()->ksort();
		foreach ($headLink as $item) {
			if (!$headLink->_isValid($item)) {
				continue;
			}
			if (!$this->isCachable($item)) {
				$items[] = $this->itemToString($item);
			} else {
				$this->cache($item);
			}
		}
		
		$itemsArray = $this->getCompiledItem();
		foreach($itemsArray as $item)		
			array_unshift($items, $this->itemToString($item));
			//$items[] = $this->itemToString($item);

		$return = implode($headLink->getSeparator(), $items);
		return $return;
	}
	
	private function getCompiledItem()
	{
		$filename = md5(serialize($this->_cache));
		$path = self::$cacheDir . $filename . (self::$compress? '_compressed' : '') . '.css';
		$pathIE = self::$cacheDir . $filename . (self::$compress? '_compressed' : '') . '_ie.css';
		if (!file_exists($path)) {
			if (!file_exists(dirname($path))) {
				ArOn_Crud_Tools_File::rmkdir(dirname($path), 0777, true);
			}
			$cssContent = '';
			foreach ($this->_cache as $css) {
				$cssContent .= file_get_contents($css['filepath']) . "\n\n";
			}
			if (self::$compress) {
				$cssContent = $this->view->minifyCSS($cssContent);
			}
			
			if (self::$compileImages){
				file_put_contents($pathIE, str_replace('url(/', 'url('.IMG_HOST_NAME.'/', $cssContent));
				preg_match_all('/url\(\/([^\)]*)\)/', $cssContent, $matches);
				$matches = array_unique($matches[1]);
				foreach($matches as $img){
					$pathinfo = pathinfo($img);
					$mime = false;
					switch (strtolower($pathinfo['extension'])){
						case 'gif':
							$mime = 'image/gif';
							break;
						case 'jpg': 
						case 'jpeg':
							$mime = 'image/jpeg';
							break;
						case 'png':
							$mime = 'image/png';
							break;
						case 'bmp':
							$mime = 'image/bmp';
							break;
					}
					if ($mime) $data = 'data:'.$mime.';base64,'.base64_encode(file_get_contents(DOCUMENT_ROOT.'/'.$img));
					else $data = IMG_HOST_NAME.'/'.$img;
					
					$cssContent = str_replace('url(/'.$img.')', 'url('.$data.')', $cssContent);
				}
				file_put_contents($path, $cssContent);
			}
			else {
				file_put_contents($path, str_replace('url(/', 'url('.IMG_HOST_NAME.'/', $cssContent));
			}
		}
		$url = str_replace(DOCUMENT_ROOT , '', $path);
		/*
		if (self::$compileImages){
			$urlIE = str_replace(DOCUMENT_ROOT , '', $pathIE);
			$items = array(
				$this->createDataStylesheet(array('href'=>$urlIE, 'media'=>'screen', 'conditionalStylesheet'=>'IE')),
				$this->createDataStylesheet(array('href'=>$url, 'media'=>'screen', 'conditionalStylesheet'=>'!IE')),
			);
			return $items;
		}
		else {
			$item = $this->createDataStylesheet(array('href'=>$url));
			return array($item);
		}
		*/
		return array($this->createDataStylesheet(array('href'=>$url)));
	}
}
