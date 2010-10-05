<?php
class Db_Services extends ArOn_Db_Table {
	protected $_primary = 'services_id';
	protected $_name = 'services';
	protected $_name_expr = "services_title";
	protected $_order_expr = 'services_order';
}