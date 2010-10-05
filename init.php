<?php
date_default_timezone_set('Europe/Kiev');
if (isset ( $_REQUEST [session_name ()] )) {
	if (! preg_match ( '/^([a-zA-Z0-9])+$/', $_REQUEST [session_name ()] )) {
		session_id ( md5 ( rand ( 0, 99999 ) . time () . rand ( 0, 9999 ) ) );
	}
}

define ( 'ROOT_PATH', dirname ( __FILE__ ) );

require_once ROOT_PATH."/application/bootstrap.php";
require_once ROOT_PATH."/application/config/Const.php";
require_once ROOT_PATH."/application/config/Path.php";
