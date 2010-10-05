<?php
class Db_CountryRegions extends ArOn_Db_Table {
	protected $_primary = 'country_region_id';
	protected $_name = 'country_regions';
	protected $_name_expr = "title";
	protected $_is_deleted = false;
	protected $_order_expr = 'title';

	protected $_dependentTables = array(
		'Db_Country'
    	);



}