<?php
switch(APPLICATION_ENVIRONMENT){
	case 'production':
		define ( 'HOST_NAME', 'http://shariki.ua' );
		define ( 'IMG_HOST_NAME', 'http://img1.shariki.ua' );
		define ( 'STATIC_HOST_NAME', 'http://static.shariki.ua' );
		define ( 'COOKIE_HOST_NAME', 'shariki.ua' );
		break;
	case 'development':
		define ( 'HOST_NAME', 'http://shariki-my.ua' );
		define ( 'IMG_HOST_NAME', 'http://img1.shariki-my.ua' );
		define ( 'STATIC_HOST_NAME', 'http://static.shariki-my.ua' );
		define ( 'COOKIE_HOST_NAME', 'shariki-my.ua' );
		break;
	case 'test':
		define ( 'HOST_NAME', 'http://shariki.test' );
		define ( 'IMG_HOST_NAME', 'http://img1.shariki.test' );
		define ( 'STATIC_HOST_NAME', 'http://static.shariki.test' );
		define ( 'COOKIE_HOST_NAME', 'shariki.test' );
		break;
	default:
		define ( 'HOST_NAME', 'http://shariki.ua' );
		define ( 'IMG_HOST_NAME', 'http://img1.shariki.ua' );
		define ( 'STATIC_HOST_NAME', 'http://static.shariki.ua' );
		define ( 'COOKIE_HOST_NAME', 'shariki.ua' );
		break;
}

define ( 'DOCUMENT_ROOT' , ROOT_PATH.'/shariki.ua');
define ( 'UPLOAD_ATTACH_FILE_TO_EMAIL_PATH' , DOCUMENT_ROOT.'/uploads/emails');
define ( 'APPLICATION_PATH', ROOT_PATH . '/application' );
define ( 'CACHE_ROOT', ROOT_PATH . '/data/cache' );
define ( 'LOG_ROOT', ROOT_PATH . '/data/log' );
define ( 'CACHE_FILE_PATH', CACHE_ROOT . '/file');
define ( 'CRUD_PATH', APPLICATION_PATH . '/models/Crud' );
define ( 'ARON_PATH', ROOT_PATH.'/library/ArOn' );
define ( 'UPLOAD_IMAGES_PATH', DOCUMENT_ROOT.'/uploads/images');
define ( 'TMP_UPLOAD_PATH', DOCUMENT_ROOT.'/uploads/tmp');
define ( 'UPLOAD_CMS_IMAGES_PATH', DOCUMENT_ROOT.'/cms/images');
define ( 'UPLOAD_CLIENT_IMAGES_PATH', DOCUMENT_ROOT.'/uploads/client/images');