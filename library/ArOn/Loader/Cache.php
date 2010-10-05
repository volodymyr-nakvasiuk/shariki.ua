<?php

if(!function_exists('__autoload')){
	function __autoload($className) {
		//require $className = str_replace ( '_', '/', $className ) . '.php';
		if(ArOn_Loader_Cache::$active)
			return false;
		$autoLoad = ArOn_Loader_Cache::getInstance();
		$autoLoad->load($className);
	}
}

class ArOn_Loader_Cache {

	protected static $_instance = null;
	
	public static $cacheDir = false;
	protected static $allowDir = false;
	protected static $lastModifiedTime = false;
	
	public static $active = false;
	public static $autoSave = false;
	
	protected static $_constructCount = 0;
	
	protected $_stats;
	protected $_logs;
	protected $_classerLog = array();
	protected $_updateLog = true;
	protected $_stmp;
	
	protected $mode;
	
	protected function __construct($mode = false){
		/*if(!class_exists('Zend_Exeption',false))
			require_once 'Zend/Exception.php';*/
		require_once 'Data/File.php';
		if(!class_exists('Zend_Json',false))
			require_once 'Zend/Json.php';
		if(self::$cacheDir === false){
    		throw new Zend_Exception('Directory for classer cache file not set');
    	}
		$this->mode = $mode;
		
		
		if(self::$cacheDir === false){
			throw new Exception('Cache directory not set');
		}		
		$this->_stmpInstance();
		$this->_logsInstance();
		$this->_statsInstance();
		$this->mode = false;
		
		$classer = $this->_stmp->get_path();
		$classer .= "/" . $this->_stmp->get_file_name();
		$this->_stmp->close();
		$this->_logs->close();
		$this->_logs->close();
		if(file_exists($classer) && is_file($classer))
			include_once ($classer);
		
	}
	
	/**
     * Singleton instance
     *
     * @return ArOn_Loader_Cache
     */
    public static function getInstance($mode = false)
    {
        if (null === self::$_instance || $mode == 'new') {        	
            self::$_instance = new self($mode);
        }

        return self::$_instance;
    }
    
    protected function _newInstance(){
    	return new self();
    }
	
    public function createClasser(){
    	
    	if($this->_stats === null){
    		$this->_statsInstance();
    	}
    	$this->_stats->close();
    	$this->mode = 'new';
    	$this->_stmpInstance();
    	self::$autoSave = true;
    	foreach ($this->_classerLog ['objects'] as $key => $object){
    		$this->_classerLog ['objects'][$key]['active'] =  0;
    		$this->load($object['class']);
    	}
    	$this->_updateLog = false;
    	$this->mode = false;
    	self::$autoSave = false;
    }
    
	public function load($className){
		$activeClassName = $className;
		if(array_key_exists($activeClassName,$this->_classerLog ['objects'])){
			$this->_classerLog ['objects'] [$activeClassName] ['count']++;
		}
		if((class_exists($activeClassName,false) || interface_exists($activeClassName,false)) && $this->mode !== 'new'){
			return;
		}			
		
		$className = str_replace ( '_', '/', $className ) . '.php';
		//var_dump($this->_classerLog);
		if(array_key_exists($activeClassName,$this->_classerLog ['objects'])){
			if($this->_classerLog ['objects'][$activeClassName]['active'] === 1)
				return true;
			if((!class_exists($activeClassName,false) && !interface_exists($activeClassName,false)) || $this->mode === 'new'){
				return $this->save($activeClassName);
			}
			return true;
		}
		
		return $this->save($activeClassName);
	}
	
	public function save($className){	
			
		$activeClassName = $className;
		$className = str_replace ( '_', '/', $className ) . '.php';
			require_once $className;		
		
		$extends = class_parents($activeClassName,false);
		$implements = class_implements($activeClassName,false);
		$this->_extendsCheck(array_merge($extends,$implements));
		if(self::$autoSave === false){
			$this->_addNewLogCountClass($activeClassName,$className);
			return true;
		}
		return $this->_addNewClass($activeClassName);
	}
	
	protected function _addNewLogCountClass($activeClassName,$activeClassDir){
		$class = array(	'class' => $activeClassName,
						'class_path' => $activeClassDir."/",
						'active' => 0,						
						'count' => 1
		);
		$this->_classerLog ['objects'] [$activeClassName] = $class;
	}
	
	protected function _addNewLogClass($pointer,$activeClassName,$activeClassPath,$created_date){
		$class = array(	'class' => $activeClassName, 
						'created_date' => $created_date, 
						'class_path' => $activeClassPath, 
						'pointer_start' => $pointer['start'], 
						'pointer_end' => $pointer['end'],
						'active' => 1,
						'count' => 1
		);
		
		if(array_key_exists($activeClassName, $this->_classerLog ['objects'])){
			$class ['count'] = $this->_classerLog ['objects'] [$activeClassName] ['count'];
		}
		$this->_classerLog ['objects'] [$activeClassName] = $class;
	}
	
	protected function _addNewClass($className){
		$activeClassName = $className;
		$className = str_replace ( '_', '/', $className ) . '.php';
		$activeClassFile = new ArOn_Loader_Data_File($className, false, true);
		if($activeClassFile->isFile() === false)			
			return false;
		$activeClassFile->pointer_set_start(0);
		$activeClassDir = $activeClassFile->get_path();
		
		if(!$this->_checkFolder($activeClassDir))
			return false;
		
		$data = $this->_getClassData($activeClassFile,$activeClassName,$activeClassDir);
					
		$pointer = array();
		$this->_stmp->reopen('write');
		$this->_stmp->pointer_set_end(0);
		$pointer['start'] = $this->_stmp->pointer_get();
		$this->_stmp->addData($data);		
		$pointer['end'] = $this->_stmp->pointer_get();
		$this->_stmp->close();
		$this->_addNewLogClass($pointer,$activeClassName,$activeClassFile->get_path() ,$activeClassFile->get_time());
		return true;
	}
	
	protected function _getClassData($activeClassFile,$activeClassName,$activeClassDir){
		$data = array();
		$data[] = "//--------------------------------------";
		$data[] = "/**     Name: " . $activeClassName . " ***";
		$data[] = "*";
		$data[] = "*   Realpath: ".$activeClassDir;
		$data[] = "*   Filename: ".$activeClassFile->get_file_name();
		$time = $activeClassFile->get_time();
		$data[] = "*   Created:  ". date("F j, Y, G:i:s", $time ) ;
		$data[] = "*";
		$data[] = "*/";
		while (($line = $activeClassFile->read_line()) !== false){			
			if(($line = $this->trim($line))){				
				$data[] = $line;
			}
		}
		$data[] = "/*  End Class   */";
		$data[] = "";
		return $data;
	}
		
	protected function _extendsCheck(array $extends){
		if(empty($extends))
			return false;
		
		foreach($extends as $extend){
			if(array_key_exists($extend,$this->_classerLog ['objects']) && $this->_classerLog ['objects'] [$extend] ['active'] === 1){
				continue;
			}
			if(class_exists($extend))
				continue;
			if(interface_exists($extend))
				continue;			
			$this->_extendLoad1($extend);
		}
	}
	
	
	protected function _extendLoad1($className){
		$this->load($className);
	}
	protected function _extendLoad2($className){
		$this->destructor();
		
		self::$active = true;
		$clone = $this->_newInstance();
		$clone->load($className);
		unset($clone);
		self::$active = false;		
		$this->_stmpInstance();
		$this->_logsInstance();
	}
	
	protected function _checkFolder($path){		
		if(self::$allowDir === false) return true;
		$path = str_replace("\\","/",$path);
		if(strpos($path,self::$allowDir) !== false)
			return true;
		
		return false;
	}
	
	protected function trim($string){
		$string = trim($string);
		if(strpos($string,"<?php") === 0){
			$string = substr($string,5);
		}
		$pos = strpos($string,"?>");		
		if($pos !== false && $pos === (strlen($string)-2)){			
			$string = substr($string,0,-2);
		}
		/*if(($pos = strpos($string,"//")) !== false){
			if($pos === 0)
				return false;
			$string = substr($string,0,($pos-1));
		}*/
		if($string == "")
			return false;
		return $string;	
	}
	
	protected function _logsInstance(){
		$this->_classerLog = array();
		$filename = "classer.log";
		if($this->mode === 'new'){
			$this->_classerLog ['constructs'] = 0;
			$this->_classerLog ['objects'] = array();
			if(file_exists(self::$cacheDir.'/'.$filename))
				unlink(self::$cacheDir.'/'.$filename);			
		}
		$this->_logs = new ArOn_Loader_Data_File(self::$cacheDir.'/'.$filename);
		
		//$this->_logs->pointer_set_start(0);
		/*while ($this->mode != 'new' && ($line = $this->_logs->read_line())){
			//var_dump(Parser_Json::decode($line));
			$line = Zend_Json::decode($line);
			if(!is_array($line))
				continue;
			$this->_classerLog['objects'] = $line;
		}*/
		
	}
	
	protected function _statsInstance(){
		$filename = "classer.stats";		
		$this->_stats = new ArOn_Loader_Data_File(self::$cacheDir.'/'.$filename);
		
		$this->_stats->pointer_set_start(0);
		while ($this->mode !== 'new' && ($line = $this->_stats->read_line())){
			//var_dump(Parser_Json::decode($line));
			$line = Zend_Json::decode($line);
			if(!is_array($line))
				continue;
			$this->_classerLog = array_merge($this->_classerLog,$line);
		}
		$this->_classerLog ['constructs']++;
	}
	
	protected function _stmpInstance(){
		$filename = "classer.php";
		if(!file_exists(self::$cacheDir.'/'.$filename))
			$this->mode = 'new';
		elseif($this->mode === 'new')
			unlink(self::$cacheDir.'/'.$filename);
		
		$this->_stmp = new ArOn_Loader_Data_File(self::$cacheDir.'/'.$filename);
		$this->_stmp->pointer_set_start(0);
		if($this->mode === 'new')
			$this->_stmp->addData("<?php");
		self::$lastModifiedTime = @filemtime(self::$cacheDir.'/'.$filename);
	}
	
	protected function _rewriteLog(){
		list($logs,$stats) = $this->_parseLogs();
		$stats = Zend_Json::encode($stats);
		$this->_logs->reopen('rewrite');
		$this->_logs->addData($logs);
		$this->_logs->close();
		
		$this->_stats->reopen('rewrite');
		$this->_stats->write($stats);
		$this->_stats->close();
	}
	
	protected function _parseLogs(){
		$stats = $this->_classerLog;
		foreach($this->_classerLog['objects'] as $name => $class){
			if(array_key_exists('active',$class) && $class['active'] == 1){
				$logs [$name] = Zend_Json::encode($class);
			}
		}
		return array($logs,$stats);
	}
	
	public function __destruct(){
		if($this->_updateLog)		
			$this->_rewriteLog();
		$this->_stmp  = null;
		$this->_stats = null;
		$this->_logs  = null;
	}
	
	public static function setAllowDir($dir){
		$dir = str_replace("\\","/",$dir);
		self::$allowDir = $dir;
	} 
	
	
}