<?php
class Db_Marketd extends ArOn_Db_Table {
	
	protected $_primary = 'marketd_id';
	protected $_name = 'marketd';
	protected $_name_expr = 'marketd_text';

	protected $_dependentTables = array();
	
	protected $_referenceMap    = array(
		'Marketc' => array(
			'columns'           => 'marketc_id',
			'refTableClass'     => 'Db_Marketc',
			'refColumns'        => 'marketc_id'
		)
	);
}