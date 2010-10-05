<?php
class ArOn_Crud_Grid_Column {

	protected $name;

	protected $title;

	protected $isSortField;

	/**
	 * ExtJs column options
	 */
	protected $_type = 'string';
	protected $_hidden;
	protected $_width;
	protected $_render_function;

	public $field;

	public $link_action;
	public $param_name;

	public $link;
	public $info_block;

	public $row_id;

	public $na = 'n/a';

	public $gridTitleField;

	public $editColumn = false;

	public $join_name;

	public $helper = array ();

	public $rowIdName = 'id';

	public $case_name = false;

	public $parentTable = true;
	public $rowClass = 'row';
	public $noOwnAttr = false;

	public $id = "{value}";

	protected $attribs = array ();
	
	/**
	 * 
	 * @var ArOn_Crud_Grid
	 */
	protected $grid;
	protected $key;
	protected $filterPrefix;

	function __construct($title, $isSort = true, $hidden = false, $width = 80, $render_function = false) {
		$this->title = $title;
		$this->isSortField = $isSort;

		$this->_hidden = $hidden;
		$this->_width = $width;
		$this->_render_function = $render_function;
	}

	public function init($grid, $key) {
		$this->grid = $grid;
		$this->key = $key;
		if ($this->name === null)
		$this->name = $key;
		if ($this->field === null)
		$this->field = $key;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getType() {
		return $this->_type;
	}

	public function getName() {
		return $this->name;
	}

	public function getField() {
		return $this->field;
	}

	public function getAction() {
		return $this->link_action;
	}

	public function getParamName() {
		return $this->param_name;
	}

	public function setField($value) {
		$this->field = $value;
		return $this;
	}

	public function setAction($action,$param_name = false) {
		$this->link_action = $action;
		$this->param_name = $param_name;
		return $this;
	}

	public function updateColumn() {
	}

	public function updateCurrentSelect(ArOn_Db_TableSelect $currentSelect) {
		$this->loadHelper ();
		return $currentSelect;
	}

	protected function getPage() {
		return $this->grid->getPage();
	}

	protected function getDirection() {
		return $this->grid->getDirection();
	}

	protected function getFilter() {
		return ($this->filterPrefix) ? $_GET [$this->filterPrefix] : $_GET;
	}

	public function isSorted() {
		return $this->isSortField;
	}

	public function isHidden() {
		return $this->_hidden;
	}

	public function getWidth() {
		return $this->_width;
	}

	public function loadHelper() {
		foreach ( $this->helper as $var => $value ) {
			$this->$var = $value;
		}
	}
	public function setRowId($value) {
		$this->row_id = $value;
	}

	protected function createActionLink($value, $title = false, $name = false) {

		$attr = '';
		if (@$this->class)
		$attr .= ' class="' . $this->class . '" ';
		if (@$this->id)
		$attr .= ' id="' . $this->id . '" ';
		if (@$this->action)
		$attr .= ' action="' . $this->action . '" ';
		if (@$this->target)
		$attr .= ' target="' . $this->target . '" ';
		if ($title)
		$attr .= ' title="' . $title . '" ';
		if ($this->info_block) {
			$attr .= ' onmouseover="infoBlock(event,\'' . $this->info_block . '\',\'' . $this->row_id . '\')" ';
		}
		if (@Crud_Grid_Abstract::$ajaxModuleName)
		$attr .= ' module="' . Crud_Grid_Abstract::$ajaxModuleName . '" ';
		$attr = str_replace ( '{value}', $this->row_id, $attr );
		$link_action = str_replace ( '{value}', $this->row_id, $this->link_action );
		$link = str_replace ( '{value}', $this->row_id, $this->link );
		if ($value === "" || $value === null)
		$value = ' - ';
		$html = '<div class="column-' . $this->name . '"><a href="' . $link . '" ' . $link_action . $attr . '>' . $value . '</a></div>';

		return $html;
	}

	public function setAttrib($name, $value) {
		$name = ( string ) $name;

		if (null === $value) {
			unset ( $this->$name );
		} else {
			$this->attribs [$name] = $value;
		}

		return $this;
	}

	public function setAttribs(array $attribs) {
		foreach ( $attribs as $key => $value ) {
			$this->setAttrib ( $key, $value );
		}

		return $this;
	}

	public function getRenderFunction(){
		return $this->_render_function;
	}

	public function render($row) {
	}

	public function setFilterPrefix($prefix){
		$this->filterPrefix = $prefix;
		return $this;
	}
}
