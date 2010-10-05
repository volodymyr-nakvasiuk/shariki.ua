<?php
class ArOn_Crud_Form_Field_ImageUpload extends ArOn_Crud_Form_Field {

	protected $_type = 'file';

	public $fileName;
	public $uploadDirectory;
	public $size;
	public $sha1 = true;

	protected $fileFullName;

	function __construct($name, $uploadDirectory, $fileName = '{id}', $title = null, $description = null, $size = '1024000', $required = null, $notEdit = false) {

		if (empty ( $_FILES ) or ! isset ( $_FILES [$name] )) {
				
			$_FILES [$name] = array ("name" => "", "type" => "", "tmp_name" => "", "error" => 4, "size" => 0 )

			;

		}
		parent::__construct ( $name, $title, $description, $required, $notEdit );
		$this->fileName = $fileName;
		$this->uploadDirectory = $uploadDirectory;
		$this->size = $size;
	}

	public function updateField() {
		parent::updateField ();

		$this->form->setAttrib ( 'enctype', 'multipart/form-data' );

		$this->saveInDataBase = false;
		$this->element->setDestination ( $this->uploadDirectory )->addValidator ( 'Count', false, 1 )->// ensure only 1 file
		addValidator ( 'Size', false, $this->size )->addValidator ( 'Extension', false, 'jpg,png,gif,jpeg,bmp' );// only JPEG, PNG, and GIFs
		//->setMultiFile(3);

		$this->form->uploadDirectory = $this->uploadDirectory;

		$this->element->helper = 'formMyFormFile';
	}

	public function postSaveAction($data = null) {
		if (! empty ( $_FILES ) && isset ( $_FILES [$this->name] ) && empty ( $_FILES [$this->name] ['error'] )) {
			$this->fileFullName = $this->fileName;
			$data = $this->form->getData ();
			$id = $this->formModel->getPrimary ();
			if(strpos($this->fileFullName,'{id}') !== false){
				$file_hash_name = ($this->sha1) ? sha1( $data [$id] ) : $data [$id] ;
				$this->fileFullName = str_replace ( '{id}', $file_hash_name, $this->fileFullName );
			}elseif (($start = strpos($this->fileFullName,'{')) !== false && ($end = strpos($this->fileFullName,'}',$start)) !== false){
				$key = substr($this->fileFullName,$start+1,$end-$start-1);
				$value = (key_exists($key, $data)) ? $data [$key] : $data [$id];
				$this->fileFullName = str_replace ( "{".$key."}", $value, $this->fileFullName );
			}elseif ($this->fileFullName === false){
				$file = explode ( ".", $_FILES [$this->name] ['name'] );
				$this->fileFullName = $file [0];
			}
			$this->fileName = $this->fileFullName;
			$file_type = end ( explode ( ".", $_FILES [$this->name] ['name'] ) );
			//$file_resize = str_replace ( $_FILES [$this->name] ['name'], IMAGE_ALTERNATIVE_PATH . "/" . $this->fileFullName . "." . $file_type, $this->element->getValue () );
			$this->fileFullName = str_replace ( $_FILES [$this->name] ['name'], $this->fileFullName . "." . $file_type, $this->element->getValue () );
			if (file_exists ( $this->fileFullName )) {
				unlink ( $this->fileFullName );
			}
			if (file_exists ( $file_resize )) {
				unlink ( $file_resize );
			}
			rename ( $this->element->getValue (), $this->fileFullName );
			//ArOn_Crud_Tools_Image::Resize ( $this->fileFullName, $file_resize, false, false );
			$def_data = array ($this->name => $this->fileName . "." . $file_type );
			if ($model = $this->form->getModel ()) {
				$model->update ( $def_data, $model->getAdapter ()->quoteInto ( $model->getPrimary () . " = ?", $data [$id] ) );
			}
		}
	}

}