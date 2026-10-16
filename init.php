<?php
define('ROOT_PATH', __DIR__ . '/');
if (!defined('DS')) {
	define('DS', '/');
}
require ROOT_PATH . 'frame/App.php';
require ROOT_PATH . 'frame/Function.php';
if (is_file(ROOT_PATH . 'vendor/autoload.php')) {
	require_once ROOT_PATH . 'vendor/autoload.php';
}
\App::init();