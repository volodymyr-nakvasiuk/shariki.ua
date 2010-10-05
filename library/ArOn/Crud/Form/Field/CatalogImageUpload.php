<?php
class ArOn_Crud_Form_Field_CatalogImageUpload extends ArOn_Crud_Form_Field_ImageUpload {
	
	protected $del_img_source = false;
	
	function __construct($name, $uploadDirectory, $fileName = '{id}', $title = null, $description = null, $size = '1024000', $required = null, $notEdit = false, $del_img_source = false) {
		parent::__construct($name, $uploadDirectory, $fileName, $title, $description, $size,$required, $notEdit);
		$this->del_img_source = $del_img_source;
	}

	protected function sha1($id = 0) {
		return md5(uniqid($id, true));
	}

	public function postSaveAction($data = null) {
		if (! empty ( $_FILES ) && isset ( $_FILES [$this->name] ) && empty ( $_FILES [$this->name] ['error'] )) {
			$this->fileFullName = $this->fileName;
			$data = $this->form->getData ();
			$id = $this->formModel->getPrimary ();
			if(strpos($this->fileFullName,'{id}') !== false){
				$file_hash_name = ($this->sha1) ? $this->sha1( $data ['id'] ) : $data ['id'] ;
				$this->fileFullName = str_replace ( '{id}', $file_hash_name, $this->fileFullName );
			}elseif (($start = strpos($this->fileFullName,'{')) !== false && ($end = strpos($this->fileFullName,'}',$start)) !== false){
				$key = substr($this->fileFullName,$start+1,$end-$start-1);
				$value = (key_exists($key, $data)) ? $data [$key] : $data [$id];
				$this->fileFullName = str_replace ( "{".$key."}", $value, $this->fileFullName );
			}elseif ($this->fileFullName === false){
				$file = explode ( ".", $_FILES [$this->name] ['name'] );
				$this->fileFullName = $file [0];
			}
				
			$file_type = strtolower(end ( explode ( ".", $_FILES [$this->name] ['name'] ) ));
			$this->fileName = $this->fileFullName.'.'.$file_type;;
			$grid = new Crud_Grid_ExtJs_Egallery(null,array('id'=>$data['id']));
			$edata = $grid->getData();
			$this->uploadDirectory = $this->uploadDirectory.'/'.$edata['data'][0]['mark_link'].'/'.$edata['data'][0]['model_link'].'/'.$edata['data'][0]['generation_link'];
			$this->fileFullName = $this->uploadDirectory.'/'       .$this->fileName;
			$file_resize_small  = $this->uploadDirectory.'/small/' .$this->fileName;
			$file_resize_middle = $this->uploadDirectory.'/middle/'.$this->fileName;
			$file_resize_big    = $this->uploadDirectory.'/big/'   .$this->fileName;
				
			if (!is_dir($this->uploadDirectory)) mkdir($this->uploadDirectory, 0777, true);
			if (!is_dir($this->uploadDirectory.'/small'))  mkdir($this->uploadDirectory.'/small' , 0777, true);
			if (!is_dir($this->uploadDirectory.'/middle')) mkdir($this->uploadDirectory.'/middle', 0777, true);
			if (!is_dir($this->uploadDirectory.'/big'))    mkdir($this->uploadDirectory.'/big'   , 0777, true);
				
			if (file_exists ( $this->fileFullName )) unlink ( $this->fileFullName );
			if (is_uploaded_file($_FILES [$this->name]['tmp_name'])) {
				move_uploaded_file($_FILES [$this->name]['tmp_name'], $this->fileFullName);
			}

			if (file_exists ( $file_resize_small ))  unlink ( $file_resize_small );
			if (file_exists ( $file_resize_middle )) unlink ( $file_resize_middle );
			if (file_exists ( $file_resize_big ))    unlink ( $file_resize_big );
				
			ArOn_Crud_Tools_Image::Resize ( $this->fileFullName, $file_resize_small, 140, 105, true);
			ArOn_Crud_Tools_Image::Resize ( $this->fileFullName, $file_resize_middle, 240, 180, true);
			ArOn_Crud_Tools_Image::Resize ( $this->fileFullName, $file_resize_big, 620, 465, true);
			
			if ($this->del_img_source){
				unlink($this->fileFullName);
			}
				
			if ($model = $this->form->getModel ()) {
				$def_data = array ($this->name => $this->fileName);
				$model->update ( $def_data, $model->getAdapter ()->quoteInto ( $model->getPrimary () . " = ?", $data ['id'] ) );
			}
		}
	}

}