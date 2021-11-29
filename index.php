<?php
define('APP_MEMORY_START', memory_get_usage());
ini_set('date.timezone', 'Asia/Shanghai');
define('APP_TIME_START', microtime(true));
define('DS', '/');
define('ROOT_PATH', strtr(__DIR__, '\\', '/').DS);
define('TEMPLATE_SUFFIX', 'html');
define('IS_CLI', false);
require ROOT_PATH.'frame'.DS.'App.php';
require ROOT_PATH.'frame'.DS.'Container.php';
require ROOT_PATH.'frame'.DS.'Helper.php';
if (is_file(ROOT_PATH.'vendor'.DS.'autoload.php')) {
	require ROOT_PATH.'vendor'.DS.'autoload.php';
}
define('IS_AJAX', request()->isAjax());
define('IS_MOBILE', request()->isMobile());
@ini_set('session.cookie_httponly', 1);
@session_start();
\App::init();
\App::send();