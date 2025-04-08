#!/usr/bin/env php
<?php 
require './init.php';
define('IS_CLI', true);
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
\App::runOver();