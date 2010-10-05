<?php
/**
 * Front Controller Plugin
 *
 * @uses	   Zend_Controller_Plugin_Abstract
 * @category   ArOn
 * @package	ArOn_Controller
 * @subpackage Plugins
 */
class ArOn_Zend_Controller_Plugin_Router extends Zend_Controller_Plugin_Abstract
{
	protected $_dir;
	protected $_default = array();
	protected $_request;
	protected $_initialConfig;
	protected $_remainingConfig;
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		// define some routes (URLs)
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$this->setRequest($request);
		$config = $this->getInitial();
		$router->addConfig($config);
	}
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$config = $this->getRemaining();
		$router->addConfig($config);
	}
	public function setDir($dir) {
		$this->_dir = $dir;
	}
	public function setDefault($default) {
		if (is_array($default)) {
			$this->_default = array_merge($this->_default, $default);
		} else {
			$this->_default[] = $default;
		}
	}
	public function setRequest($request) {
		$this->_request = $request;
	}
	public function getInitial() {
		if ($this->_initialConfig == null) {
			$this->parseIniDir();
		}
		return $this->_initialConfig;
	}
	public function getRemaining() {
		if ($this->_remainingConfig == null) {
			$this->parseIniDir();
		}
		return $this->_remainingConfig;
	}
	protected function parseIniDir() {
		$files = $this->getFiles();
		$this->_default;
		$this->_default[] = $this->determineInitial();
		$this->_initialConfig = new Zend_Config(array(), true);
		$this->_remainingConfig = new Zend_Config(array(), true);
		if (is_array($files)) {
			foreach ($files as $file) {
				$routerFile = $this->compilePath($file);
				if (in_array($file, $this->_default)) {
					$this->_initialConfig->merge(new Zend_Config_Ini($routerFile));
				} else {
					$this->_remainingConfig->merge(new Zend_Config_Ini($routerFile));
				}
			}
		}
	}
	protected function getFiles() {
		if (is_dir($this->_dir)) {
			$dir = new DirectoryIterator($this->_dir);
			$files = array();
			foreach($dir as $fileInfo) {
				if(!$fileInfo->isDot() && $fileInfo->isFile()) {
					$files[] = $fileInfo->__toString();
				}
			}
			return $files;
		}
		return false;
	}
	protected function getOtherRoutes() {
		$routes->merge(new Zend_Config_Ini($routerFile));
	}
	protected function determineInitial() {
		if ($this->_request) {
			$uri = $this->_request->getRequestUri();
			$base = $this->_request->getBasePath() . '/';
			$request = str_replace($base, '', $uri);
			$requestParts = explode('/', $request);
			$lang = $requestParts[0];
			$section = $requestParts[1];
			if (!empty($section) && $section == 'user') {
				return 'user.ini';
			}
		}
		return false;
	}
	protected function compilePath($file) {
		return $this->_dir . '/' . $file;
	}
}
