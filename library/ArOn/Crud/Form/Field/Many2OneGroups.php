<?php
class ArOn_Crud_Form_Field_Many2OneGroups extends ArOn_Crud_Form_Field {

	protected $_type = 'multiselect';

	public $model;

	protected $key;

	public $id;

	public $nullElement = false;

	public $onchange;

	public $optionName;
	public $category;
	public $categoryName;
	public $emptyCategory;
	public $emptyItem;
	public $where;

	public $field_edit = false;

	protected $required = false;

	public $many2manyModel;

	public function updateField() {
		parent::updateField ();

		if (is_string ( $this->model ))
		$this->model = ArOn_Crud_Tools_Registry::singleton ( $this->model );
		if (! is_object ( $this->model ))
		return;
		//$data = (!empty($this->id))?$this->model->getListStandart($this->model->e($this->key,$this->id)):$this->model->getListStandart();


		$select = $this->model->select ();
		$select = $this->updateCurrentSelect ( $select );
		$options = ArOn_Crud_Tools_Multiselect::prepareOptions ( $select, $this->optionName, $this->category, $this->categoryName, $this->where, $this->emptyCategory, $this->emptyItem );

		if ($this->hidden or $this->notEdit or $this->category) {
			$this->element->setRegisterInArrayValidator ( false );
		}
		$data = (($result = $this->model->fetchAll ( $select )) === null) ? array() : $result->toArray();
		$groups = array ();
		$this->model = ArOn_Crud_Tools_Registry::singleton ( $this->model );
		$id = $this->model->getPrimary ();
		foreach ( $data as $row ) {
			$groups [$row [$id]] = $row ['group_string'];
		}
		$this->element->setAttrib ( 'groups', $groups );
		//if($this->nullElement or $this->required)	$this->element->addMultiOption('', 'none');
		if (! empty ( $this->onchange ))
		$this->element->setAttrib ( 'onchange', $this->onchange );
		$this->element->addMultiOptions ( $options );

		$this->element->helper = 'MyFormSelectJava';

		$this->saveInDataBase = false;
	}
	public function updateCurrentSelect($select) {
		$this->loadHelper ();
		$this->many2manyModel = ArOn_Crud_Tools_Registry::singleton ( $this->many2manyModel );
		$refModel = $this->many2manyModel->getReferenceEx ( $this->parentModel );
		$this->key = $refModel ['columns'];
		$select->columnsJoinMany ( $this->many2manyModel, 'group_string', $this->key );
		return $select;
	}
}
