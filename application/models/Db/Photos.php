<?php
class Db_Photos extends ArOn_Db_Table {
	protected $_primary = 'photos_id';
	protected $_name = 'photos';
	protected $_name_expr = "photos_core";
	protected $_order_expr = 'photos_order';
}