<?php

/**
 * CarController
 *
 * @author
 * @version
 */

class Cms_IndexController extends Abstract_Controller_CmsController {

	public function indexAction(){
		$this->view->headScript()
		->appendFile('/js/ckeditor/ckeditor.js')
		->appendFile('/js/ckfinder/ckfinder.js')
		->appendFile('/cms/js/extjs/adapter/ext/ext-base.js')
		->appendFile('/cms/js/extjs/ext-all.js')
		->appendFile('/cms/js/extjs/ux/utils/Ext.ux.utils.nv.js')
		->appendFile('/cms/js/extjs/desktop/StartMenu.js')
		->appendFile('/cms/js/extjs/desktop/TaskBar.js')
		->appendFile('/cms/js/extjs/desktop/Desktop.js')
		->appendFile('/cms/js/extjs/desktop/App.js')
		->appendFile('/cms/js/extjs/desktop/Module.js')
		->appendFile('/cms/js/extjs/ux/Ext.ux.IconManager.js')
		->appendFile('/cms/js/desktop/sample.js')
		->appendFile('/cms/js/extjs/form/MultiSelect.js')
		->appendFile('/cms/js/extjs/form/ImageMultiSelect.js')
		->appendFile('/cms/js/extjs/form/ColorField.js')
		->appendFile('/cms/js/extjs/form/CKEditor.js')
		->appendFile('/cms/js/extjs/ux/CKFinder.js')
		->appendFile('/cms/js/extjs/ux/FileUploadField.js')
		->appendFile('/cms/js/extjs/ux/plugins/Ext.ux.Admin.plugins.js')
		//->appendFile('/cms/js/extjs/ux/plugins/Ext.ux.plugins.HtmlEditorImageInsert.js')
		//->appendFile('/cms/js/extjs/ux/plugins/Ext.ux.plugins.HtmlEditorFullScreen.js')
		//->appendFile('/cms/js/extjs/ux/plugins/Ext.ux.HtmlEditor.Plugins-0.2-all.js')
		->appendFile('/cms/js/extjs/ux/Ext.ux.UploadDialog.js')//http://max-bazhenov.com/dev/upload-dialog-2.0/index.php
		->appendFile('/cms/js/extjs/locale/ru.utf-8.js')
		->appendFile('/cms/js/extjs/src/locale/ext-lang-ru.js')
		->appendFile('/cms/js/funcs.js')
		;

		$this->view->headLink()	->appendStylesheet('/cms/js/extjs/resources/css/ext-all-car.css')
		->appendStylesheet('/cms/css/desktop.css')
		->appendStylesheet('/cms/css/icons.css')
		->appendStylesheet('/cms/css/file-upload.css')
		->appendStylesheet('/cms/css/Ext.ux.UploadDialog.css')
		;
		
		$grid = new Crud_Grid_ExtJs_Shortcuts();
		$this->view->shortcuts = $grid->getData();
	}


}
