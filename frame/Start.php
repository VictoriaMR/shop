<?php
define('DS', '/');
require ROOT_PATH.'frame'.DS.'App.php';
require ROOT_PATH.'frame'.DS.'Container.php';
require ROOT_PATH.'frame'.DS.'Helper.php';
define('IS_CLI', isCli());
require ROOT_PATH.'frame'.DS.'Env.php';
require ROOT_PATH.'frame'.DS.'Config.php';
if (is_file(ROOT_PATH.'vendor'.DS.'autoload.php')) {
	require ROOT_PATH.'vendor'.DS.'autoload.php';
}
App::init();
if (!IS_CLI) {
	@session_start();
	define('IS_MOBILE', request()->isMobile());
	define('IS_AJAX', request()->isAjax());
	App::run();
}