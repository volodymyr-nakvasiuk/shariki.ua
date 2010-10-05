<?php
class Db_SiteModule extends ArOn_Db_Table {
	protected $_primary = 'module_id';
	protected $_name = 'site_modules';
	protected $_name_expr = "module_name";
	protected $_is_deleted = false;
	protected $_order_expr = 'module_name';

	protected $_dependentTables = array(
		'Db_SiteController'
    	);



}