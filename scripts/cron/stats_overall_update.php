<?php
include 'zend_cron_init.php';
$time_start = microtime(true);

//$stats = new Tools_SiteStatistic();
$stats = new Tools_SiteStatistic('day');
$time_end = microtime(true);
$time = $time_end - $time_start;

?>