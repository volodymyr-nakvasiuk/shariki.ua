<?php
class Db_View_NewsPhotos extends Db_Pphotos {

	protected $_where = "`pphotos`.photos_type = 'news' ";
	
	protected $_referenceMap    = array(
		'News' => array(
			'columns'           => 'photos_parent_id',
			'refTableClass'     => 'Db_News',
			'refColumns'        => 'news_id'
		)
	);
}