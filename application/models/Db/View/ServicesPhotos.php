<?php
class Db_View_ServicesPhotos extends Db_Pphotos {

	protected $_where = "`pphotos`.photos_type = 'services' ";
	
	protected $_referenceMap    = array(
		'Services' => array(
			'columns'           => 'photos_parent_id',
			'refTableClass'     => 'Db_Services',
			'refColumns'        => 'services_id'
		)
	);
}