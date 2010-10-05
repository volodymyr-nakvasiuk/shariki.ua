<?php
class Db_Client extends ArOn_Db_Table {
	protected $_primary = 'client_id';
	protected $_name = 'client';
	protected $_name_expr = "client_name";
	//protected $_is_deleted = "is_deleted";
	protected $_aclWhere = "%s";
	protected $_aclColumn = 'client_id';
	
	protected $_dependentTables = array(
		'Db_Car',
		'Db_View_Car',
		'Db_View_NewCar',
		'Db_View_AllCar',
		'Db_View_AllNewCar',
		'Db_View_AllPrice',
		'Db_ClientPrice',
		'Db_ClientMessages',
		'Db_ReferalIpStats',
		'Db_Statistic',
		'Db_ReferalClientStats',
		'Db_AdsPriceSms'
    	);
	protected $_referenceMap    = array(
		'Region' => array(
            'columns'           => 'client_region_id',
            'refTableClass'     => 'Db_Region',
            'refColumns'        => 'region_id'
            ),
        'AclUserd' => array(
            'columns'           => 'acl_role_id',
            'refTableClass'     => 'Db_AclUsers',
            'refColumns'        => 'id'
            )
	);
}