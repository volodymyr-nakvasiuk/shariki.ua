<?php
set_include_path (
	APPLICATION_PATH . '/models' . PATH_SEPARATOR .
	APPLICATION_PATH . '/modules/default/controllers' . PATH_SEPARATOR .
	APPLICATION_PATH . '/modules/cms' . PATH_SEPARATOR .
	APPLICATION_PATH . '/modules' . PATH_SEPARATOR .
	APPLICATION_PATH . PATH_SEPARATOR .
	ROOT_PATH . '/library' . PATH_SEPARATOR .
	ROOT_PATH . '/library/ArOn/Else' . PATH_SEPARATOR .
	get_include_path ()
);

