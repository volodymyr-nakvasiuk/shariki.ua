<?php
define ('ConfigType', 'cron');
require_once('../../init.php');
echo APPLICATION_ENVIRONMENT."\r\n\r\n";
Bootstrap::setupSelfConst();
Bootstrap::setupPhpIni();
Bootstrap::setupRegistry ();
Bootstrap::setupConfiguration ();
Bootstrap::setupDatabase ();
Bootstrap::setupCache();

//ArOn_Crud_Tools_Register::registerData();

set_time_limit(0);
?>