<?php
class Db_Indexmenu extends ArOn_Db_Table {
	protected $_primary = 'indexmenu_id';
	protected $_name = 'indexmenu';
	protected $_name_expr = "indexmenu_title";
	protected $_order_expr = 'indexmenu_order';
}