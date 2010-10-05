<?php
class Crud_Form_ExtJs_Client extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_View_Client';
	protected $_title = 'Пользователь';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/client/save/';
		$this->actionName = 'client';

		$this->fields = array(
			'id' => new ArOn_Crud_Form_Field_Numeric('client_id', 'Id') ,
			'name' => new ArOn_Crud_Form_Field_Text('client_name',"Имя"),
			//'aclusers' => new ArOn_Crud_Form_Field_Many2Many('client_acl_group_id','Права доступа'),
			'tel' => new ArOn_Crud_Form_Field_Text('client_tel','Тел-ны (разделитель ";")'),
			'email' => new ArOn_Crud_Form_Field_Mail('client_email','Почта',null,true),
			//'url' => new ArOn_Crud_Form_Field_Text('client_url','Сайт'),
			'region' => new ArOn_Crud_Form_Field_Many2One('client_region_id','Область'),
			//'place' => ArOn_Crud_Form_Field_Many2One('client_place_id','Город'),'Db_View_Place'
			'place' => new ArOn_Crud_Form_Field_Text('client_place','Город'),
			'addres' => new ArOn_Crud_Form_Field_Text('client_addr','Адрес'),
			'info' => new ArOn_Crud_Form_Field_TextArea('client_info', 'Дополнительно'),
			'password' => new ArOn_Crud_Form_Field_Text('client_password','Пароль'),
			//'client_photos' => new ArOn_Crud_Form_Field_AdminImageUpload('client_photos',UPLOAD_CLIENT_IMAGES_PATH , '{id}', true,'Изображение'),
		);
		$this->fields['region']->model = 'Db_Region';
		/*$this->fields['aclusers']->helper = array(
			'model' => 'Db_AclUsers',
			'workingModel' => 'Db_AclUserClients',
		);*/
		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');
		/*if(!empty($this->actionId)){
		 $this->fields['affiliate_id']->notEdit = true;
		 $this->_alternative_data['update_date'] = date('Y-m-d');
		 }else{
		 $this->fields['status']->checked = true;
		 $this->_alternative_data['create_date'] = date('Y-m-d');
		 $this->_alternative_data['update_date'] = date('Y-m-d');
		 }*/
		//$this->fields['affiliate_id']->nullElement = array('0' => 'All');
		$this->groups = array('0' => array_keys($this->fields));
		parent::init();


	}

	/*
	 public function saveValidData(){
	 $presave_data = $this->getData();
	 $result = parent::saveValidData();
	 if (is_numeric($result)){
	 $grid = new Crud_Grid_ExtJs_Emodel(null,array('id'=>$result));
	 $data = $grid->getData();
	 $row_data = $data['data'][0];
	 $path = UPLOAD_CATALOG_IMAGES_PATH.'/'.$row_data['mark_link'   ].'/';
	 //$path = str_replace("/", "\\", $path);  //just for Windows
	 if (!is_dir($path)) mkdir($path, 0777, true);
	 if ($this->is_new_data){
	 if(!is_dir($path.$row_data['model_link'])){
	 mkdir($path.$row_data['model_link'], 0777, true);
	 }
	 }
	 elseif ($row_data['model_link'] != $presave_data['model_link']){
	 if(is_dir($path.$presave_data['model_link'])){
	 rename($path.$presave_data['model_link'],$path.$row_data['model_link']);
	 }
	 else {
	 mkdir($path.$row_data['model_link'], 0777, true);
	 }
	 }
	 }
	 return $result;
	 }
	 */
}
