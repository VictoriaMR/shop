#!/usr/bin/env php
<?php 
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
define('DS', '/');
define('ROOT_PATH', strtr(__DIR__, '\\', '/').DS);
define('APP_PATH', ROOT_PATH.'app'.DS);
define('IS_CLI', true);
require ROOT_PATH.'frame'.DS.'Start.php';
if (!isset($argv[1])) exit('class error');
if (!isset($argv[2])) exit('function error');
$func = $argv[2];
$param = [];
foreach ($argv as $key=>$value) {
	if ($key < 3) continue;
	$temp = explode('=', $value);
	$param[$temp[0]] = $temp[1];
}
\App::make($argv[1], $param)->$func();
dd($param);
\App::runOver();