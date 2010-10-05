<?php
class ArOn_Crud_Grid_ExtJs_MultiGrid extends ArOn_Crud_Grid_ExtJs {

	protected $_parent_store = 'store_center';

	protected $_width = '320';

	public function setParentStore($store_name){
		$this->_parent_store = $store_name;
		return $this;
	}

	protected function renderTitle() {
		$html = $this->renderGrid();
		$html .= $this->renderAssociation();
		return $html;
	}

	protected function renderAssociation() {
		$html = $this->getItem().".on('cellclick', function(){
			var m = ".$this->getItem().".getSelectionModel().getSelections();
			if(m.length == 1) {
				".$this->_parent_store.".baseParams = ".$this->_parent_store.".baseParams || {};
            	".$this->_parent_store.".baseParams['".$this->_idProperty."'] = m[0].id;
            	".$this->_parent_store.".reload({params: {start:0}});
            }
		});";
		return $html;
	}

}
