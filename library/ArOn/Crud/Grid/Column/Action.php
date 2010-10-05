<?php
class ArOn_Crud_Grid_Column_Action extends ArOn_Crud_Grid_Column {

	public $action;
	public $target;
	protected $action_name;
	protected $action_title;
	protected $params;

	public function __construct($icon, $link, $options = null) {
		$this->title = $icon;
		$this->link = $link;
		$this->isSortField = false;
		//		$this->action_name = $this->name;
		$this->action_title = $this->title;
		$this->title = '';
		$this->class = 'grid-action2';
		if (is_array ( $options )) {
			$this->helper = $options;
		}
	}

	public function render($row) {
		$value = $this->row_id;
		$attr = '';
		if (! empty ( $this->params ))
		$this->params = "?" . $this->params;

		if (@$this->target)
		$attr .= ' target="' . $this->target . '" ';
		if (@$this->class)
		$attr .= ' class="' . $this->class . '" ';
		if (@$this->id)
		$attr .= ' id="' . $this->id . '" ';
		if (@$this->action)
		$attr .= ' action="' . $this->action . '" ';
		if (@$this->gridTitleField)
		$attr .= ' title="' . $row [$this->gridTitleField] . '" ';
		if (@Crud_Grid_Abstract::$ajaxModuleName)
		$attr .= ' module="' . Crud_Grid_Abstract::$ajaxModuleName . '" ';

		$attr = str_replace ( '{value}', $value, $attr );
		$this->link_action_t = str_replace ( '{value}', $value, $this->link_action );
		$this->link_t = str_replace ( '{value}', $value, $this->link );
		if ($this->action_name == '#') {
			$html = '<a href="' . $this->action_name . $this->params . '" ' . $this->link_action_t . $attr . '>' . $this->action_title . '</a>';
		} elseif (! empty ( $this->action_name ) and $this->action_name != '-') {
			$html = '<a href="' . $this->link_t . $this->action_name . '/' . $value . $this->params . '" ' . $this->link_action . $attr . '>' . $this->action_title . '</a>';
		} else {
			$html = '<a href="' . $this->link_t . $this->params . '" ' . $this->link_action . $attr . '>' . $this->action_title . '</a>';
		}
		return $html;
	}
}