<?php
class Db_Marketc extends ArOn_Db_Table {
	
	protected $_primary = 'marketc_id';
	protected $_name = 'marketc';
	protected $_name_expr = 'marketc_title';

	protected $_dependentTables = array(
		'Db_Marketd'
	);

}