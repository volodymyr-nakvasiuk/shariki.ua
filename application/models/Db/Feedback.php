<?php
class Db_Feedback extends ArOn_Db_Table {
	protected $_primary = 'feedback_id';
	protected $_name = 'feedback';
	protected $_name_expr = "feedback_title";
	protected $_order_expr = 'feedback_order';
}