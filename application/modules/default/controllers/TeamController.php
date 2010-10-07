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

}