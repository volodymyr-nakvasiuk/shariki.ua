<?php
class Db_Country extends ArOn_Db_Table {
	protected $_primary = 'country_id';
	protected $_name = 'countries';
	protected $_name_expr = 'title';
	//protected $_is_deleted = "is_deleted";
	protected $_order_expr = 'country_region_id';

	protected $_dependentTables = array(
		
    	);

    	protected $_referenceMap    = array(
	        'Country_Regions' => array(
	            'columns'           => 'country_region_id',
	            'refTableClass'     => 'Db_CountryRegions',
	            'refColumns'        => 'region_id'
	            )
         );

}