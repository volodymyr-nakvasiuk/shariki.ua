<?php
class ArOn_Zend_View extends Zend_View {
	
	protected $_minifyTypes = array();
	protected $_minify = array();
	public $lastTmplt = '';
	
	public function setMinify($resourseExts = 'html', $minifyPath = '', $resourseType = ''){
		$resourseType = strtoupper($resourseType);
		if ($minifyPath){
			$minifyPath = rtrim($minifyPath, '/') . '/';
			if (!is_dir($minifyPath))
				mkdir($minifyPath, 0777, true);
		}
		if (!is_array($resourseExts)) $resourseExts = array($resourseExts);
		foreach ($resourseExts as &$resourseExt){
			$resourseExt = strtolower($resourseExt);
			if (isset($this->_minify[$resourseExt])){
				$type = $this->_minify[$resourseExt];
				$this->_minifyTypes[$type]['exts'] = array_diff($this->_minifyTypes[$type]['exts'], array($resourseExt));
			}
			$this->_minify[$resourseExt] = $resourseType;
		}
		if (isset($this->_minifyTypes[$resourseType])){
			$this->_minify = array_diff($this->_minify, $this->_minifyTypes[$resourseType]['exts']);
		}
		$this->_minifyTypes[$resourseType] = array('path'=>$minifyPath, 'exts'=>$resourseExts);
	}
	
	protected function _minifyFile($file){
		$pathinfo = pathinfo($file);
		$ext = strtolower($pathinfo['extension']);
		if (isset($this->_minify[$ext]) && $this->_minify[$ext]){
			if ($path = $this->_minifyTypes[$this->_minify[$ext]]['path']){
				$fileContent = file_get_contents($file);
				$file = $path . md5($fileContent) . '.' . $ext;
				$func = 'minify'.$this->_minify[$ext];
				if (!file_exists($file)) {
					file_put_contents($file, $this->$func($fileContent));
				}
			}
		}
		return $file;
	}
	
	public function minifyHTML($fileContent){
		return implode("\n", array_filter(array_map('trim', explode("\n", $fileContent))));
	}
	
	public function minifyCSS($fileContent){
		return Minify_CSS::minify(
			$fileContent, 
			array('preserveComments' => false)
		);
	}
	
	public function minifyJS($fileContent){
		return JSMin::minify($fileContent);
	}
	
	protected function _run(){
		parent::_run($this->_minifyFile(func_get_arg(0)));
	}
	
	public function getMinifyType($resourseType = false){
		$resourseType = strtoupper($resourseType);
		if (isset($this->_minifyTypes[$resourseType])){
			return $this->_minifyTypes[$resourseType]['path'];
		}
		else {
			return '';
		}
	}
	
	public function run($name){
		ob_start(); 
		$this->preRender($name);
		$this->_run($name);
		$r = ob_get_contents();
		ob_end_clean();
		return $r;
	}
	
	public function render($name){
		$this->preRender($name);
		return parent::render($name);
	}
	
	public function preRender($name){
		if (
			Zend_Controller_Front::getInstance()->getRequest()->getControllerName()=='error' &&
			Zend_Controller_Front::getInstance()->getRequest()->getActionName()=='error'
		){
			//Zend_Controller_Front::getInstance()->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
			Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(404);
			$this->message = 'Page not found'; 
		}
	}
	
	protected function _jsHTML($html){
		return '"'.addcslashes(str_replace(array("\n", "\r"), array("", ""), $html), "\\\'\"\t").'"';
	}
	
	public function jsRender($name){
		return $this->_jsHTML($this->render($name));
	}
	
	public function jsRun($name){
		return $this->_jsHTML($this->run($name));
	}
	
	public function templatizate($t){
		$this->lastTmplt = $t;
		return str_replace(array('"','$',"'"),array('&#34;','&#36;', "&#44;"),$t);
	}
}