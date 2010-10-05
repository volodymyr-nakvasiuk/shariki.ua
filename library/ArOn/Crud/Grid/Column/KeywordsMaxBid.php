<?php
class ArOn_Crud_Grid_Column_KeywordsMaxBid extends ArOn_Crud_Grid_Column {

	private $_bidFieldName;

	public function __construct($title, $bidFieldName) {

		$this->_bidFieldName = $bidFieldName;

		parent::__construct ( $title );

	}

	public function updateCurrentSelect(ArOn_Db_TableSelect $currentSelect) {

		$currentSelect->joinLeft ( 'ad_keyword_store', 'ad_keyword.id = ad_keyword_store.ad_keyword_id', array () )->joinLeft ( 'ad_keyword_maxbid', 'ad_keyword_store.crc32 = ad_keyword_maxbid.crc32', array ('maxbid' => new Zend_Db_Expr ( 'max(maxbid)' ) ) )->group ( 'ad_keyword.id' );
		;

		return $currentSelect;

	}

	public function render(array &$row) {

		$value = floatval ( $row [$this->key] );
		$id = $row ['id'];

		if (! empty ( $value ))
		$res = '<a href="javascript:void(0)" onclick="$(\'#' . $this->_bidFieldName . '-' . $id . '\').val(\'' . number_format ( $value, 2 ) . '\')">$' . number_format ( $value, 2 ) . '</a>';
		else
		$res = '';

		return $res;

	}

}