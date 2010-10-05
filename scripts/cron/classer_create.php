<?php
date_default_timezone_set('Europe/Kiev');
error_reporting ( E_ALL ^ E_NOTICE );
ini_set('display_errors',1);
if(function_exists('apc_clear_cache'))
	apc_clear_cache();
$path = realpath("../../init.php");
define ( 'ROOT_PATH', dirname ( $path ) );

require_once ROOT_PATH."/application/config/Const.php";
require_once ROOT_PATH."/application/config/Path.php";

require_once 'ArOn/Loader/Cache.php';

ArOn_Loader_Cache::$cacheDir = ROOT_PATH ."/library/Classer";
ArOn_Loader_Cache::setAllowDir(ROOT_PATH ."/library/");
$mode = false;
$autoLoad = ArOn_Loader_Cache::getInstance($mode);
$autoLoad->createClasser();
?>