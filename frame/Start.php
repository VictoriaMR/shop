<?php
define('DS', '/');
require ROOT_PATH.'frame'.DS.'App.php';
require ROOT_PATH.'frame'.DS.'Container.php';
require ROOT_PATH.'frame'.DS.'Helper.php';
define('IS_CLI', isCli());
if (is_file(ROOT_PATH.'vendor'.DS.'autoload.php')) {
	require ROOT_PATH.'vendor'.DS.'autoload.php';
}
App::init();
if (!IS_CLI) {
	@ini_set('session.cookie_httponly', 1);
	@session_start();
	define('IS_MOBILE', request()->isMobile());
	define('IS_AJAX', request()->isAjax());
	App::run();
}