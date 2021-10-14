#!/usr/bin/env php
<?php 
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
define('DS', '/');
define('ROOT_PATH', strtr(__DIR__, '\\', '/').DS);
ini_set('date.timezone', 'Asia/Shanghai');
if (empty($argv[1])) {
	exit('class error');
}
if (empty($argv[2])) {
	exit('function error');
}
$class = $argv[1];
$func =$argv[2];
$param = [];
foreach ($argv as $key=>$value) {
	if ($key < 3) continue;
	$temp = explode('=', $value);
	$param[array_shift($temp)] = implode('=', $temp);
}
define('IS_CLI', true);
require ROOT_PATH.'frame'.DS.'App.php';
require ROOT_PATH.'frame'.DS.'Container.php';
require ROOT_PATH.'frame'.DS.'Helper.php';
if (is_file(ROOT_PATH.'vendor'.DS.'autoload.php')) {
	require ROOT_PATH.'vendor'.DS.'autoload.php';
}
@ini_set('session.cookie_httponly', 1);
@session_start();
\App::init();
\App::make($class, $param)->$func();
\App::runOver();