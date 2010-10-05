<?php
class Db_Seo extends ArOn_Db_Table {
	protected $_primary = 'seo_id';
	protected $_name = 'seo';
	protected $_name_expr = "seo_description";
	protected $_is_deleted = false;
	protected $_order_expr = 'seo_action_id';

	protected $_dependentTables = array(
    );

    protected $_referenceMap    = array(
        'Action' => array(
            'columns'           => 'seo_action_id',
            'refTableClass'     => 'Db_SiteActs',
            'refColumns'        => 'action_id'
            )
     );

}