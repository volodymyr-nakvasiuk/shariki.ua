<?php
class Db_SiteController extends ArOn_Db_Table {
	protected $_primary = 'controller_id';
	protected $_name = 'site_controllers';
	protected $_name_expr = 'controller_name';
	//protected $_is_deleted = "is_deleted";
	protected $_order_expr = 'controller_module_id';

	protected $_dependentTables = array(
		'Db_SiteActs'
    	);

    protected $_referenceMap    = array(
        'Module' => array(
            'columns'           => 'controller_module_id',
            'refTableClass'     => 'Db_SiteModule',
            'refColumns'        => 'module_id'
            )
    );

}