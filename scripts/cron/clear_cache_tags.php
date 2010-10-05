<?php
include 'zend_cron_init.php';

$model = new Db_SiteCacheTags();
$model->delete("`cache_tag_created_date` < DATE_SUB(now(), INTERVAL 12 HOUR)");