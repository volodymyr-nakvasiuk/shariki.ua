<?php
class Db_Static extends ArOn_Db_Table {
	protected $_primary = 'static_id';
	protected $_name = 'static';
	protected $_name_expr = "static_title";
	protected $_order_expr = 'static_id';
	
	protected $_dependentTables = array(
	);

	protected $_referenceMap    = array(
		'Action' => array(
			'columns'           => 'static_action',
			'refTableClass'     => 'Db_SiteActs',
			'refColumns'        => 'action_id'
		)
	);
}