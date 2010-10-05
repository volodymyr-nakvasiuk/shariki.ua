<?php
class Db_Calendar extends ArOn_Db_Table {
	protected $_primary = 'calendar_id';
	protected $_name = 'calendar';
	protected $_name_expr = "calendar_title";
	protected $_order_expr = 'calendar_date';
}