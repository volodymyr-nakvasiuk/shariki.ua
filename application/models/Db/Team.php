<?php
class Db_Team extends ArOn_Db_Table {
	protected $_primary = 'team_id';
	protected $_name = 'team';
	protected $_name_expr = "team_name";
	protected $_order_expr = 'team_order';
}