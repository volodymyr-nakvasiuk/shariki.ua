<?php
ini_set('memory_limit','1000M');
include 'zend_cron_init.php';
include 'db.php';

$backupFile = str_replace('\\', '/', ROOT_PATH).'/data/backup/db/statistic/backuped_'.date('Y-m-d-His').'.csv';
if (!is_dir(dirname($backupFile))) mkdir(dirname($backupFile), 0777, true);

$where = "`stat_date`<'".date('Y-m')."-01 00:00:00'";

echo $sql = "SELECT * FROM `statistic` WHERE ".$where." INTO OUTFILE '".$backupFile."';";
$result = mysql_query($sql);
echo "<br/>\n";

if ($result){
	//echo $sql = "DELETE FROM `statistic` WHERE ".$where.";";
	echo $sql = "TRUNCATE `statistic`;";
	mysql_query($sql);
}
else {
	echo mysql_errno() . ": " . mysql_error(). "<br/>\n";
}
