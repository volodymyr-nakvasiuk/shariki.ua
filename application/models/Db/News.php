<?php
class Db_News extends ArOn_Db_Table {
	protected $_primary = 'news_id';
	protected $_name = 'news';
	protected $_name_expr = "news_title";
	protected $_order_expr = 'news_created_date';
}