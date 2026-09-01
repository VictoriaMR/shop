#!/usr/bin/env php
<?php 
defined('IS_CLI') || define('IS_CLI', true);
require __DIR__ . '/init.php';

if (!isset($argv[1])) exit('class error');
if (!isset($argv[2])) exit('function error');

$class = $argv[1];
$func = $argv[2];
\App::make($class)->$func();
\App::runOver();