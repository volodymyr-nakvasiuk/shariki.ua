<?php
class ArOn_Crud_Grid_Column_ActionQuestionnaire extends ArOn_Crud_Grid_Column_JoinMany {

	protected $_image;
	protected $_link;

	public function __construct($image, $link, $title, $rules = null, $tableField = null, $orderBy = null, $separator = null, $limit = null) {

		$this->_image = $image;
		$this->_link = $link;

		parent::__construct ( $title, $rules, $tableField, $orderBy, $separator, $limit );

	}

	public function render($row) {

		$value = $row [$this->name];

		$link = str_replace ( '{value}', $row ['id'], $this->_link );

		$html = '';
		if ($value > 0) {
			$html = '<a href="' . $link . '" >' . $this->_image . '</a>';
		}

		return $html;

	}
}