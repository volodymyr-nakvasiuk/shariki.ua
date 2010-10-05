<?php
/**
 * @license Public domain
 */
class ArOn_Zend_View_Helper_MagicHeadScript extends Zend_View_Helper_HeadScript
{
	private static $cacheDir;
	private static $combine = 1;
	private static $compress = 1;
	private static $symlinks = array();
	private static $clearInc = 0; //folder with inc js files 
	
	private $_cache = array();
	
	static public function setConfig($compress = 1, $symlinks = array(), $clearInc = 0)
	{
		self::$symlinks = $symlinks;
		self::$compress = $compress;
		self::$clearInc = $clearInc?rtrim($clearInc, '/').'/':0;
	}
	
	protected function setup(){
		self::$cacheDir = $this->view->getMinifyType('JS');
		self::$combine = self::$cacheDir?1:0;
	}
	
	public function magicHeadScript()
	{
		$this->setup();
		if (self::$combine) {
			return $this->toString();
		} else {
			return $this->view->headScript();
		}
	}
	
	public function itemToString($item, $indent, $escapeStart, $escapeEnd)
	{
		$attrString = '';
		if (!empty($item->attributes)) {
			foreach ($item->attributes as $key => $value) {
				if (!$this->arbitraryAttributesAllowed()
					&& !in_array($key, $this->_optionalAttributes))
				{
					continue;
				}
				if ('defer' == $key) {
					$value = 'defer';
				}
				$attrString .= sprintf(' %s="%s"', $key, ($this->_autoEscape) ? $this->_escape($value) : $value);
			}
		}

		$type = ($this->_autoEscape) ? $this->_escape($item->type) : $item->type;
		$html  = $indent . '<script type="' . $type . '"' . $attrString . '>';
		if (!empty($item->source)) {
			  $html .= PHP_EOL . $indent . '	' . $escapeStart . PHP_EOL . $item->source . $indent . '	' . $escapeEnd . PHP_EOL . $indent;
		}
		$html .= '</script>';

		if (isset($item->attributes['conditional'])
			&& !empty($item->attributes['conditional'])
			&& is_string($item->attributes['conditional']))
		{
			$html = '<!--[if ' . $item->attributes['conditional'] . ']> ' . $html . '<![endif]-->';
		}

		return $html;
	}

	public function searchJsFile($src)
	{
		$path = DOCUMENT_ROOT . $src;
		if (is_readable($path)) {
			return $path;
		} 
		foreach (self::$symlinks as $virtualPath => $realPath) {
			$path = str_replace($virtualPath, $realPath, "/$src");
			if (is_readable($path)) {
				return $path;
			} 
		}
		return false;
	}	
	
	public function isCachable($item)
	{
		if (isset($item->attributes['conditional'])
			&& !empty($item->attributes['conditional'])
			&& is_string($item->attributes['conditional']))
		{
			return false;
		}
		
		if ($item->attributes['non-cache']){
			return false;
		}
		
		if (!isset($item->attributes['src']) || !$this->searchJsFile($item->attributes['src'])) {
			return false;
		}
		return true;
	}
	
	public function cache($item)
	{
		if (!empty($item->source)) {
			$key = md5($item->source);
			if(!isset($this->_cache[$key]))
				$this->_cache[$key] = $item->source;
		} else {
			$filePath = $this->searchJsFile($item->attributes['src']);
			$key = md5($filePath);
			if(!isset($this->_cache[$key]))
				$this->_cache[$key] = array(
					'filepath' => $filePath,
					'mtime' => filemtime($filePath)
				);
		}
	}
	
	public function toString($indent = null)
	{
		$headScript = $this->view->headScript();
		
		$indent = (null !== $indent)
				? $headScript->getWhitespace($indent)
				: $headScript->getIndent();

		if ($this->view) {
			$useCdata = $this->view->doctype()->isXhtml() ? true : false;
		} else {
			$useCdata = $headScript->useCdata ? true : false;
		}
		$escapeStart = ($useCdata) ? '//<![CDATA[' : '//<!--';
		$escapeEnd   = ($useCdata) ? '//]]>'	   : '//-->';

		$items = array();
		$headScript->getContainer()->ksort();
		foreach ($headScript as $item) {
			if (!$headScript->_isValid($item)) {
				continue;
			}
			if (!$this->isCachable($item)) {
				$items[] = $this->itemToString($item, $indent, $escapeStart, $escapeEnd);
			} else {
				$this->cache($item);
			}
		}
		
		//array_unshift($items, $this->itemToString($this->getCompiledItem(), $indent, $escapeStart, $escapeEnd));
		$items[] = $this->itemToString($this->getCompiledItem(), $indent, $escapeStart, $escapeEnd);

		$return = implode($headScript->getSeparator(), $items);
		return $return;
	}
	
	private function getCompiledItem()
	{
		$filename = md5(serialize($this->_cache));
		$path = self::$cacheDir . $filename . (self::$compress ? '_compressed' : '') . '.js';
		if (!file_exists($path)) {
			//...debug("Combine javascripts to $path...");
			if (!file_exists(dirname($path))) {
				ArOn_Crud_Tools_File::rmkdir(dirname($path), 0777, true);
			}
			$jsContent = '';
			foreach ($this->_cache as $js) {
				if (is_array($js)) {
					if (self::$clearInc) if (strpos($js['filepath'], "/js.js") !== false) continue;
					$jsContent .= file_get_contents($js['filepath']) . "\n\n";
					//...debug($js['filepath'] . ' ... OK');
				} else {
					if (self::$clearInc) if (strpos($js, "/js.js") !== false) continue;
					$jsContent .= $js . "\n\n";
					//...debug('Inline JavaScript ... OK');
				}
			}
			if (self::$clearInc) $jsContent = $this->replaceInc($jsContent);
			if (self::$compress) {
				$jsContent = $this->view->minifyJS($jsContent);
			}
			file_put_contents($path, $jsContent);
		}
		$url = str_replace( DOCUMENT_ROOT , STATIC_HOST_NAME , $path );
		$item = $this->createData('text/javascript', array('src'=>$url));
		return $item;
	}
	
	private function replaceInc($content){
		$modules = array();
		$modules = $this->getIncModules($content, $modules);
		
		while (($s = strpos($content, "js.include")) !== false){
			$e = strpos($content, ";", $s)+1;
			$fnk = substr($content, $s, $e-$s);
			$module = trim(str_replace(array("(", "\"", "'", ")", ";"), array("", "", "", "", ""), substr($fnk, 10)));
			$rpls = '';
			if (!array_key_exists($module, $modules)){
				$rpls = file_get_contents(self::$clearInc.str_replace('.', '/', $module).'.js');
				$modules[$module] = true;
			}
			$content = str_replace($fnk, $rpls, $content);
			
			$modules = $this->getIncModules($content, $modules);
		}
		return $content;
	}
	
	private function getIncModules(&$content, $modules){
		while (($s = strpos($content, "js.module")) !== false){
			$e = strpos($content, ";", $s)+1;
			$fnk = substr($content, $s, $e-$s);
			$module = trim(str_replace(array("(", "\"", "'", ")", ";"), array("", "", "", "", ""), substr($fnk, 9)));
			$modules[$module] = true;
			$content = str_replace($fnk, "", $content);
		}
		return $modules;
	}
}