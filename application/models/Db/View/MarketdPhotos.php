<?php
class Db_View_MarketdPhotos extends Db_Pphotos {

	protected $_where = "`pphotos`.photos_type = 'market' ";

	protected $_referenceMap    = array(
		'Marketd' => array(
			'columns'           => 'photos_parent_id',
			'refTableClass'     => 'Db_Marketd',
			'refColumns'        => 'marketd_id'
		)
	);
}