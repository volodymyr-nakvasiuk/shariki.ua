<?php
class Crud_Window_ExtJs_Ckfinder extends ArOn_Crud_Window_ExtJs {
	protected $windowTitle = 'Файловый менеджер';
	public $ajaxActionName = 'ckfinder';

	protected $_items = array(
		array(
			//'id'=>'ID',
			//'region'=>'center',
			//'name'=>'win_ckfinder',
			//'item_var'=>'win_ckfinder',
			'element'=>'ckfinder',
			'width'=>'100%',
			'config'=>array(
				
			),
		),
	);
}