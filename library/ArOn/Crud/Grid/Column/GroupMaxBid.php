<?php
class ArOn_Crud_Grid_Column_GroupMaxBid extends ArOn_Crud_Grid_Column {

	private $_bidFieldName;

	public function __construct($title, $bidFieldName) {

		$this->_bidFieldName = $bidFieldName;

		parent::__construct ( $title );

	}

	public function updateCurrentSelect(ArOn_Db_TableSelect $currentSelect) {

		$currentSelect->joinLeft ( 'ad_keyword', 'ad_group.id = ad_keyword.ad_group_id AND ad_keyword.is_deleted = 0', array () )->joinLeft ( 'ad_keyword_store', 'ad_keyword.id = ad_keyword_store.ad_keyword_id', array () )->joinLeft ( 'ad_keyword_maxbid', 'ad_keyword_store.crc32 = ad_keyword_maxbid.crc32', array ('maxbid' => new Zend_Db_Expr ( 'max(maxbid)' ) ) )->group ( 'ad_group.id' );//					  ->where('ad_keyword.is_deleted = 0');


		return $currentSelect;

	}

	public function render(array &$row) {

		$value = floatval ( $row [$this->key] );
		$id = $row ['id'];

		if (! empty ( $value ))
		$res = '<a href="ja	vascript:void(0)" onclick="$(\'#' . $this->_bidFieldName . '-' . $id . '\').val(\'' . number_format ( $value, 2 ) . '\')">$' . number_format ( $value, 2 ) . '</a>';
		else
		$res = "";

		return $res;

	}

}