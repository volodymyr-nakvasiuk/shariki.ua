<?php
//require_once 'ArOn/Db/Table.php';

class Db_Currency extends ArOn_Db_Table {

	protected $_primary = 'currency_id';
	protected $_name = 'currency';
	protected $_name_expr = 'currency_name';
	//protected $_name_expr = "CONCAT_WS(' - ',`metafeed`.id,`metafeed`.name)";
	//protected $_is_deleted = "is_deleted";

	protected $_dependentTables = array(
		'Db_Car',
		'Db_FuelPrice',
		'Db_View_Bookmarks',
		'Db_View_NewCar'
		);

}