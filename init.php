<?php
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
define('DS', '/');
define('ROOT_PATH', strtr(__DIR__,'\\',DS).DS);
require ROOT_PATH.'frame/App.php';
require ROOT_PATH.'frame/Helper.php';
file_exists(ROOT_PATH.'vendor/autoload.php') && require ROOT_PATH.'vendor/autoload.php';
\App::init();