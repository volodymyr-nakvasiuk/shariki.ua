<?php
//date_default_timezone_set('Europe/Kiev');
if (isset($_POST['PHPSESSID']))
	$_COOKIE['PHPSESSID'] = $_POST['PHPSESSID'];
if (substr_count($_SERVER ['SERVER_NAME'], '-my') == 0 && !(isset($configType) && $configType == 'cron')){
	require_once ROOT_PATH ."/library/Classer/classer.php";
}
if(!function_exists('__autoload')){
	function __autoload($className) {
		$className = explode('_', $className);
		if (count($className)>1 && $className[0] == 'Client' && strpos($className[1], 'Controller')){
			$className[0] = 'client/controllers';
		}
		require implode('/', $className).'.php';
	}
}
/*ini_set ( 'display_errors', true );
require_once 'ArOn/Loader/Cache.php';
ArOn_Loader_Cache::$cacheDir = ROOT_PATH ."/library/Classer";
ArOn_Loader_Cache::setAllowDir(ROOT_PATH ."/library/");
$mode = false;
if(array_key_exists('loader',$_GET) && $_GET['loader'] === 'new')
	$mode = 'new';
$autoLoad = ArOn_Loader_Cache::getInstance($mode);*/
//$autoLoad->createClasser();die;

class Bootstrap {

	/**
	 * @var Zend_Controller_Front
	 */
	public static $frontController = null;
	
	public static $root = '';
	public static $registry = null;

	public static function run() {
		self::prepare ();
		$response = self::$frontController->dispatch ();
		self::sendResponse ( $response );
	}

	public static function prepare() {
		self::setupSelfConst();
		self::setupPhpIni();
		self::setupFirePHP();
		//self::usePageCacheStatic();
		self::setupRegistry ();
		//ArOn_Core_Debug::getGenerateTime('setupRegistry');
		self::setupConfiguration ();
		self::setupDatabase ();
		self::setupCache();
		self::setupFrontController ();
		self::setupErrorHandler ();
		self::setupView ();
		self::setupSessions ();
		//self::setupTranslation();
		self::setupRoutes ();
		self::setupAcl ();
		self::setupController ();
	}
	
	public static function setupSelfConst() {
		self::$root = dirname ( dirname ( __FILE__ ) );
	}

	public static function setupEnvironment() {
		if (defined('APPLICATION_ENVIRONMENT')) return false;
		
		//init APPLICATION_ENVIRONMENT
		if(!defined('ConfigType')){
			if (isset($_GET['aen'])){
				$configType = $_GET['aen'];
			}
			else {
				$configType = 'production';
				if (isset($_SERVER ['SERVER_NAME'])){
					$esn = explode('.',$_SERVER ['SERVER_NAME']);
					$c = count($esn)-1;
					$d = $esn[$c-1].'.'.$esn[$c];
					switch ($d){
						case 'shariki-my.ua': $configType = 'development'; break;
						case 'shariki.test': $configType = 'test'; break;
					}
				}
			}
		}
		else {
			$configType = ConfigType;
		}
		
		//init APPLICATION_ERROR_MODE
		if (isset($_GET['aem']) || isset($_COOKIE['aem'])){
			if (isset($_GET['aem'])) {
				$_COOKIE['aem'] = $_GET['aem'];
				setcookie('aem', $_GET['aem'], time()+3600, '/');
			}
			$errorMode = $_COOKIE['aem'];
		}
		else {
			switch ($configType){
				case 'cron':
				case 'test':
				case 'development':
					$errorMode = 'on'; break;
				default:
					$errorMode = 'off';
			}
		}
		
		//init APPLICATION_FIREPHP_MODE
		if (isset($_GET['afm'])){
			$firePhpMode = $_GET['afm'];
		}
		else {
			switch ($configType){
				case 'test':
					$firePhpMode = 'on'; break;
				default:
					$firePhpMode = 'off';
			}
		}
		
		//init APPLICATION_CACHE_MODE
		if (isset($_GET['acm'])){
			$cacheMode = $_GET['acm'];
		}
		else {
			switch ($configType){
				case 'cron':
				case 'development':
					$cacheMode = 'off'; break;
				default:
					$cacheMode = 'off';
			}
		}
		
		//init APPLICATION_COMPRESS_MODE
		if (isset($_GET['amm'])){
			$compressMode = $_GET['amm'];
		}
		else {
			switch ($configType){
				case 'cron':
				case 'development':
					$compressMode = 'off'; break;
				default:
					$compressMode = 'on';
			}
		}
		
		//init VIEW_EXTRAINFO_MODE
		if (isset($_GET['vem'])){
			$extraInfoMode = $_GET['vem'];
		}
		else {
			switch ($configType){
				case 'test':
				case 'development':
					$extraInfoMode = 'on'; break;
				default:
					$extraInfoMode = 'off';
			}
		}
		
		define ('APPLICATION_ENVIRONMENT', $configType );
		define ('APPLICATION_ERROR_MODE' , $errorMode );
		define ('APPLICATION_FIREPHP_MODE' , $firePhpMode );
		define ('APPLICATION_CACHE_MODE' , $cacheMode );
		define ('APPLICATION_COMPRESS_MODE' , $compressMode );
		define ('VIEW_EXTRAINFO_MODE' , $extraInfoMode );
	}

	public static function setupPhpIni(){
		if(APPLICATION_ERROR_MODE == 'on'){
			error_reporting(E_ALL ^ E_NOTICE);
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			/*ini_set('xdebug.remote_enable',1);
			ini_set('xdebug.remote_autostart','On');

			ini_set('xdebug.profiler_enable','On');
			ini_set('xdebug.profiler_output_dir',"C:\Program Files\Zend\ZendServer\tmp\xdebug\cachegrid");
			ini_set('xdebug.profiler_append','On');
			ini_set('xdebug.profiler_output_name',"%t.cachegrind.out");
			
			ini_set('xdebug.auto_trace','On');
			ini_set('xdebug.trace_format',0);
			ini_set('xdebug.collect_params',1);
			ini_set('xdebug.collect_return', 1);
			ini_set('xdebug.collect_includes',1);
			ini_set('xdebug.trace_options',1);
			ini_set('xdebug.trace_output_dir',"C:\Program Files\Zend\ZendServer\tmp\xdebug\trace");
			ini_set('xdebug.trace_output_name',"%t.trace.xt");
			ini_set('xdebug.show_local_vars',1);
			ini_set('xdebug.show_local_vars','On');
			//ini_set('xdebug.dump.SERVER','HTTP_HOST, SERVER_NAME');
			ini_set('xdebug.dump_globals','On');
			ini_set('xdebug.collect_params','4');*/

			//ini_set('xdebug.show_exception_trace','On');
		}else{
			error_reporting(0);
			ini_set('display_errors', 0);
			ini_set('display_startup_errors', 0);
		}
	}
	
	public static function setupCache(){
		if (APPLICATION_CACHE_MODE == 'on'){
			ArOn_Cache_Type_Abstract::$root = ROOT_PATH;
			$cache = new ArOn_Cache_Adapter();
			$cacher = $cache->getCacher();
		}
	}
	
	public static function setupFirePHP(){
		if (APPLICATION_FIREPHP_MODE == 'on') {
			/* Turn on all errors and STRICT notices */
			ArOn_Core_Debug::setEnabled(true);
			//ArOn_Core_Debug::getGenerateTime('Begin');
		} else {
			/* Turn off all errors */
			
		}
	}

	public static function setupRegistry() {
		self::$registry = new Zend_Registry ( array (), ArrayObject::ARRAY_AS_PROPS );
		Zend_Registry::setInstance ( self::$registry );
	}

	public static function setupConfiguration() {
		$config = new Zend_Config_Ini ( self::$root . '/application/config/main.ini', APPLICATION_ENVIRONMENT );
		self::$registry->configuration = $config;
	}

	public static function setupFrontController() {
		self::$frontController = Zend_Controller_Front::getInstance ();
		self::$frontController->addModuleDirectory ( self::$root . '/application/modules' );
		$response = new Zend_Controller_Response_Http ( );
		$response->setHeader ( 'Content-Type', 'text/html; charset=UTF-8', true );
		self::$frontController->setResponse ( $response );
		self::$frontController->returnResponse(true);
	}

	public static function setupErrorHandler() {
		if(APPLICATION_ERROR_MODE == 'on')
			self::$frontController->throwExceptions ( true );
		else
			self::$frontController->throwExceptions ( false );
		
		self::$frontController->registerPlugin ( new Zend_Controller_Plugin_ErrorHandler ( array ('module' => 'default', 'controller' => 'error', 'action' => 'error' ) ) );
		//return true;
		//$writer = new Zend_Log_Writer_Firebug ( );
		//$logger = new Zend_Log ( $writer );
		//Zend_Registry::set ( 'logger', $logger );
	}

	public static function setupController() {
		// place to put in your Controll Action Helpers
		// ex: Zend_Controller_Action_HelperBroker::addHelper(new ArOn_Controller_Action_Helper_AuthUsers());
		//self::$frontController->registerPlugin(new ArOn_Zend_Controller_Plugin_Acl());
		self::$frontController->registerPlugin(new ArOn_Zend_Controller_Plugin_Http_Conditional(), 101);
	}

	public static function setupView() {
		$view = new ArOn_Zend_View ( array ('encoding' => 'UTF-8' ) );
		$view->addHelperPath ( 'ArOn/View/Helper', 'ArOn_View_Helper_' );
		//$view->setEncoding('utf-8');
		$viewRendered = new Zend_Controller_Action_Helper_ViewRenderer ( $view );

		Zend_Controller_Action_HelperBroker::addHelper ( $viewRendered );

		Zend_Layout::startMvc ( array ('layoutPath' => self::$root . '/application/layouts', 'layout' => 'frontend' ) );
		
		$view->addHelperPath( ARON_PATH .'/Zend/View/Helper', 'ArOn_Zend_View_Helper' );
		ArOn_Zend_View_Helper_MagicHeadScript::setConfig(1, array(), DOCUMENT_ROOT.'/js');
		ArOn_Zend_View_Helper_MagicHeadLink::setConfig(1, array(), true);
		if (APPLICATION_COMPRESS_MODE == 'on'){
			$view->setMinify(array('html', 'phtml'), DOCUMENT_ROOT.'/cache/html', 'HTML');
			$view->setMinify('css', DOCUMENT_ROOT.'/cache/css', 'CSS');
			$view->setMinify('js', DOCUMENT_ROOT.'/cache/js', 'JS');
		}
	}

	public static function sendResponse(Zend_Controller_Response_Http $response) {
		$response->sendResponse ();
		return;
		try {
			if (@strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
				ob_start();
				$response->sendResponse ();
				$output = gzencode(ob_get_contents(), 9);
				ob_end_clean();
				header('Content-Encoding: gzip');
				echo $output;
			}
			else {
				$response->sendResponse ();
			}
		} catch (Exeption $e) {
			if (Zend_Registry::isRegistered('Zend_Log')) {
				Zend_Registry::get('Zend_Log')->err($e->getMessage());
			}
			$message = $e->getMessage() . "\n\n" . $e->getTraceAsString();
			/* trigger event */
		}
	}

	public static function setupDatabase() {
		$config = self::$registry->configuration;
		self::$registry->database = Zend_Db::factory ( $config->db->adapter, $config->db->toArray () );
		self::$registry->database->query("SET CHARACTER SET 'utf8'");
		self::$registry->database->query("SET NAMES 'utf8'");
		
		if (APPLICATION_CACHE_MODE == 'off'){
			ArOn_Cache_Type_Db::$tableBackhend = ArOn_Cache_Type_Abstract::FILE;
		}
		
		Zend_Db_Table::setDefaultAdapter ( self::$registry->database );
		
		if(APPLICATION_ENVIRONMENT!='cron'){
			$frontendOptions = array ('automatic_serialization' => true );
			$backendOptions = array ('cache_dir' => CACHE_ROOT . '/db_table/metadata' );
			//Next, set the cache to be used with all table objects
			ArOn_Db_Table::setMetadataCacheOptions ( 'Core', ArOn_Cache_Type_Db::$tableBackhend , $frontendOptions, $backendOptions );
		}
	}

	public static function setupSessions() {
		// Now set session save handler to our custom class which saves the data in MySQL database
		if(ArOn_Zend_Session_Main::checkClientAgent()){
			$sessionManager = new ArOn_Session_Manager ( );
			Zend_Session::setOptions ( array ('gc_probability' => 1, 'gc_divisor' => 5000) );
			Zend_Session::setSaveHandler ( $sessionManager );
		}
		
		$defSession = new ArOn_Zend_Session_Main ( 'Main', true );
		Zend_Registry::set ( 'defSession', $defSession);
	}

	public static function setupTranslation() {
		$options = array ('scan' => Zend_Translate::LOCALE_FILENAME, 'disableNotices' => true );
		$translate = new Zend_Translate ( 'gettext', self::$root . '/application/languages/', 'auto', $options );
		Zend_Registry::set ( 'Zend_Translate', $translate );

		if (self::$frontController) {
			self::$frontController->registerPlugin ( new ArOn_Controller_Plugin_Language ( ) );
		}
	}

	public static function setupRoutes() {
		$router = self::$frontController->getRouter ();
		
		$configFile = APPLICATION_ENVIRONMENT .'.ini';
		$config = new Zend_Config_Ini ( self::$root . '/application/config/routes/'.$configFile, 'routes');
		$router->addConfig ( $config, 'routes' );
	}

	public static function setupAcl() {
		//ArOn_Crud_Form::$ajaxModuleName = self::$frontController->getRequest()->getModuleName();
		self::$frontController->registerPlugin ( new ArOn_Zend_Controller_Plugin_Acl ( ) );
	}
	
	protected function setupZFDebug()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('ZFDebug');

        $options = array(
            'plugins' => array(
                'Variables',
                'File' => array('base_path' => '/РїСѓС‚СЊ/Рє/РїСЂРѕРµРєС‚Сѓ'),
                'Memory',
                'Time',
                'Registry',
                'Exception',
                'Html',
            )
        );

        if ($this->hasPluginResource('db')) {
            $this->bootstrap('db');
            $db = $this->getPluginResource('db')->getDbAdapter();
            $options['plugins']['Database']['adapter'] = $db;
        }

        if ($this->hasPluginResource('cache')) {
            $this->bootstrap('cache');
            $cache = $this-getPluginResource('cache')->getDbAdapter();
            $options['plugins']['Cache']['backend'] = $cache->getBackend();
        }

        $debug = new ZFDebug_Controller_Plugin_Debug($options);

        $this->bootstrap('frontController');
        $frontController = $this->getResource('frontController');
        $frontController->registerPlugin($debug);
    }
	
	
}

Bootstrap::setupEnvironment();

if (!function_exists('get_called_class')):

  function get_called_class($debug = false) {
    $bt = debug_backtrace();
    //debug($bt);
    $l = 0;
    do {
        $l++;
        $lines = file($bt[$l]['file']);
        $callerLine = $lines[$bt[$l]['line']-1];
        //debug($callerLine);
        preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/',
                   $callerLine,
                   $matches);
    } while ($matches[1] == 'parent' && $matches[1]);
  		if($debug){
				var_dump($matches);
				die;
		}
    return $matches[1];
  }

endif;

function do_dump($value,$level=0)
{
  if ($level==-1)
  {
    $trans[' ']='&there4;';
    $trans["\t"]='&rArr;';
    $trans["\n"]='&para;;';
    $trans["\r"]='&lArr;';
    $trans["\0"]='&oplus;';
    return strtr(htmlspecialchars($value),$trans);
  }
  if ($level==0) echo '<pre>';
  $type= gettype($value);
  echo $type;
  if ($type=='string')
  {
    echo '('.strlen($value).')';
    $value= do_dump($value,-1);
  }
  elseif ($type=='boolean') $value= ($value?'true':'false');
  elseif ($type=='object')
  {
    $props= get_class_vars(get_class($value));
    echo '('.count($props).') <u>'.get_class($value).'</u>';
    foreach($props as $key=>$val)
    {
      echo "\n".str_repeat("\t",$level+1).$key.' => ';
      do_dump($value->$key,$level+1);
    }
    $value= '';
  }
  elseif ($type=='array')
  {
    echo '('.count($value).')';
    foreach($value as $key=>$val)
    {
      echo "\n".str_repeat("\t",$level+1).do_dump($key,-1).' => ';
      do_dump($val,$level+1);
    }
    $value= '';
  }
  echo " <b>$value</b>";
  if ($level==0) echo '</pre>';
}