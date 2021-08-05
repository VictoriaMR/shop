#!/usr/bin/env php
<?php 
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
define('ROOT_PATH', strtr(__DIR__, '\\', '/').'/');
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
require ROOT_PATH.'frame/Start.php';
make($class, $param)->$func();
\App::runOver();