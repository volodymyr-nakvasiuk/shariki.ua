<?php
class Cms_BrowseController extends Abstract_Controller_CmsController {
	public function treeAction(){
		$this->_helper->viewRenderer->setNoRender();
		if (isset($_REQUEST['node'])){
			$path = DOCUMENT_ROOT.$_REQUEST['node'];
			$id = $_REQUEST['node'];
				
			if (is_dir($path)){
				if ($dh = opendir($path)) {
					$result = array();
					while (($file = readdir($dh)) !== false) {
						if ($file == '.' || $file == '..' || !is_dir($path.'/'.$file)) continue;
						$result_param = array();
						$result_param[] = '"text":"'.$file.'"';
						$result_param[] = '"id":"'.$id.'/'.$file.'"';
						$result_param[] = '"leaf":false';

						$result[] = '{'.implode(', ',$result_param).'}';
					}
					closedir($dh);
					echo '['.implode(', ',$result).']';
				}
				else echo '{success: false, message: "Не могу открыть заданный вами путь!"}';
			}
			else echo '{success: false, message: "Заданный вами путь несуществует!"}';
		}
		else echo '{success: false, message: "Неверный идентификатор узла дерева!"}';
	}

	public function getfilesAction(){
		$this->_helper->viewRenderer->setNoRender();
		if (isset($_REQUEST['tree_val'])){
			$path = DOCUMENT_ROOT.$_REQUEST['tree_val'];
			$id = $_REQUEST['tree_val'];
			if (isset($_REQUEST['exts'])) {
				$exts = explode(',',str_replace(' ','',strtolower($_REQUEST['exts'])));
			}
			if (is_dir($path)){
				if ($dh = opendir($path)) {
					$result = array();
					$result['success'] = 'true';
					$result['results'] = 0;
					while (($file = readdir($dh)) !== false) {
						//$mb_file = mb_convert_encoding($file, 'UTF-8', 'windows-1251');
						$mb_file = $file;
						if (!empty($exts) && is_array($exts)){
							$pathinfo = pathinfo($file);
							$ext = strtolower($pathinfo['extension']);
							if (!in_array($ext,$exts)) continue;
						} 
						if ($file == '.' || $file == '..' || is_dir($path.'/'.$file)) continue;
						$result_param = array();
						$result_param["displayText"] = $mb_file;
						$result_param["optionValue"] = $id.'/'.$mb_file;
						$result['rows'][] = $result_param;
						$result['results'] = $result['results']+1;
					}
					closedir($dh);
					echo Zend_Json::encode($result);
				}
				else echo '{success: false, message: "Не могу открыть заданный вами путь!"}';
			}
			else echo '{success: false, message: "Заданный вами путь несуществует!"}';
		}
		else echo '{success: false, message: "Неверный идентификатор узла дерева!"}';
	}
}