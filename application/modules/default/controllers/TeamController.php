<?php
class TeamController extends Abstract_Controller_FrontendController {
	
	public function init(){
		parent::init();
		$this->view->activeMenu = 'team';
	}
	
	public function indexAction(){
		$team = new Init_Team();
		$this->view->data = $team->getData();
	}

    public function detailAction(){
		$team = new Init_Team();
		$this->view->data = $team->getData();
		$id = $this->_request->getParam('id');
		$this->view->selectedId = $id?$id:$this->view->data['0']['team_id'];

		$this->view->layouts['left']["left_menu"] = array('inc/menu/team', 100);
	}

}