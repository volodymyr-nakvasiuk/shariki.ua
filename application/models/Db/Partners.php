<?php
class Db_Partners extends ArOn_Db_Table {
	protected $_primary = 'partners_id';
	protected $_name = 'partners';
	protected $_name_expr = "partners_title";
	protected $_order_expr = 'partners_order';
}