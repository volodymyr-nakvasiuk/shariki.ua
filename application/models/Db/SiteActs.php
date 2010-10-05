<?php
class Db_SiteActs extends ArOn_Db_Table {
	protected $_primary = 'action_id';
	protected $_name = 'site_actions';
	protected $_name_expr = "action_name";
	protected $_is_deleted = false;
	protected $_order_expr = 'action_controller_id';

	protected $_dependentTables = array(
		'Db_Seo',
		'Db_Static'
	);

    protected $_referenceMap    = array(
        'Controller' => array(
            'columns'           => 'action_controller_id',
            'refTableClass'     => 'Db_SiteController',
            'refColumns'        => 'controller_id'
            )
     );

}