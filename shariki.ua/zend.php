<?php
//ob_start();

require '../init.php';
Bootstrap::run();
/*
$file_name = TMP_UPLOAD_PATH.'/php_requests.log';
file_put_contents($file_name,date("\n".'H-i-s :'."\n"), FILE_APPEND);
file_put_contents($file_name,ob_get_contents() , FILE_APPEND);
file_put_contents($file_name,date("\n-----------------------------\n"), FILE_APPEND);
ob_flush();
*/