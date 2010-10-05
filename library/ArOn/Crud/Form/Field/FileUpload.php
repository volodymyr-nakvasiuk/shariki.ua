<?php
class ArOn_Crud_Form_Field_FileUpload extends ArOn_Crud_Form_Field {

	protected $_type = 'file';

	public $fileName;
	public $uploadDirectory;
	public $size;
	public $extensions;
	public $sha1 = true;
	protected $_db_just_filename;
	
	protected $fileFullName;
	
	function __construct($name, $uploadDirectory, $fileName = '{id}', $db_just_filename = true, $title = null, $description = null, $size = '1024000', $required = null, $notEdit = false, $width = 150, $extensions = 'swf') {

		if (empty ( $_FILES ) or ! isset ( $_FILES [$name] )) {
				
			$_FILES [$name] = array ("name" => "", "type" => "", "tmp_name" => "", "error" => 4, "size" => 0 )

			;

		}
		parent::__construct ( $name, $title, $description, $required, $notEdit );
		$this->fileName = $fileName;
		$this->uploadDirectory = $uploadDirectory;
		$this->extensions = $extensions;
		$this->size = $size;
		$this->_db_just_filename = $db_just_filename;
	}
	
	protected function sha1($id = 0) {
		return md5(uniqid($id, true));
	}

	public function updateField() {
		parent::updateField ();

		$this->form->setAttrib ( 'enctype', 'multipart/form-data' );

		$this->saveInDataBase = false;
		$this->element->setDestination ( $this->uploadDirectory )->addValidator ( 'Count', false, 1 );// ensure only 1 file

		if (! empty ( $this->extensions ))
		$this->element->addValidator ( 'Extension', false, $this->extensions );

		//if (! empty ( $this->size ))
		//$this->element->addValidator ( 'Size', false, $this->size );

		$this->form->uploadDirectory = $this->uploadDirectory;

		$this->element->helper = 'formMyFormFile';

	}

	/*public function postSaveAction($data = null) {

		if (! empty ( $_FILES ) && isset ( $_FILES [$this->name] ) && empty ( $_FILES [$this->name] ['error'] )) {
				
			$data = $this->form->getData ();
			$id = $this->formModel->getPrimary ();
			$def_data = array ($this->name => $_FILES [$this->name] ['name'] );
			if ($model = $this->form->getModel ()) {
				$model->update ( $def_data, $model->getAdapter ()->quoteInto ( $model->getPrimary () . " = ?", $data [$id] ) );
			}

		}

	}*/
	
	public function postSaveAction($data = null) {
		if (! empty ( $_FILES ) && isset ( $_FILES [$this->name] ) && empty ( $_FILES [$this->name] ['error'] )) {
			$this->fileFullName = $this->fileName;
			$data = $this->form->getData ();
			$model = $this->form->getModel ();
				
			if(strpos($this->fileFullName,'{sha}') !== false){
				$file_hash_name = ($this->sha1) ? $this->sha1( $this->form->actionId ) : $this->form->actionId;
				$this->fileFullName = str_replace ( '{sha}', $file_hash_name, $this->fileFullName );
			}
			while (($start = strpos($this->fileFullName,'{')) !== false && ($end = strpos($this->fileFullName,'}',$start)) !== false){
				$key = substr($this->fileFullName,$start+1,$end-$start-1);
				$value = (key_exists($key, $data)) ? $data [$key] : $this->form->actionId;
				$this->fileFullName = str_replace ( "{".$key."}", $value, $this->fileFullName );
			}
			if ($this->fileFullName === false){
				$file = explode ( ".", $_FILES [$this->name] ['name'] );
				$this->fileFullName = $file [0];
			}
				
			$file_type = strtolower(end ( explode ( ".", $_FILES [$this->name] ['name'] ) ));
			$this->fileName = $this->fileFullName.'.'.$file_type;
			$zend_upload_dir = $this->uploadDirectory;
			$this->fileFullName = $this->uploadDirectory.'/'.$this->fileName;
			$pathinfo = pathinfo($this->fileFullName);
			$this->uploadDirectory = $pathinfo['dirname'];
			if ($this->_db_just_filename) $this->fileName = $pathinfo['basename'];
				
			/* Debuger: */
			/*
			 echo '$file_type = '.$file_type."\n";
			 echo '$this->fileName = '.$this->fileName."\n";
			 echo '$this->uploadDirectory = '.$this->uploadDirectory."\n";
			 echo '$this->fileFullName = '.$this->fileFullName."\n";
			 echo '$file_save_name = '.$file_save_name."\n";
			 exit;
			 */
				
			if (!is_dir($this->uploadDirectory)) mkdir($this->uploadDirectory, 0755, true);
				
			if (file_exists ( $this->fileFullName )) unlink ( $this->fileFullName );
			if (is_uploaded_file($_FILES [$this->name]['tmp_name'])) {
				move_uploaded_file($_FILES [$this->name]['tmp_name'], $this->fileFullName);
			}
			elseif (is_file($zend_upload_dir.'/'.$_FILES [$this->name] ['name'])) {
				rename($zend_upload_dir.'/'.$_FILES [$this->name] ['name'], $this->fileFullName);
			}
				
			$def_data = array ($this->name => $this->fileName);
			$model->update ( $def_data, $model->getAdapter ()->quoteInto ( $model->getPrimary () . " = ?", $this->form->actionId) );
			$_FILES [$this->name] ['name'] = $this->fileName;
		}
	}

}