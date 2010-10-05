<?php
class Db_Ip2Country extends ArOn_Db_Table {

	protected $_primary = array('IP_FROM','IP_TO');
	protected $_name = 'ip_to_country';
	protected $_name_expr = 'COUNTRY';
	//protected $_name_expr = "CONCAT_WS(' - ',`metafeed`.id,`metafeed`.name)";
	//protected $_is_deleted = "is_deleted";

	protected $_dependentTables = array();

}