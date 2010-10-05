<?php
class Db_Shortcuts extends ArOn_Db_Table {
	protected $_primary = 'shortcut_id';
	protected $_name = 'cms_shortcuts';
	protected $_name_expr = "shortcut_text";
	protected $_order_expr = 'shortcut_id';
}