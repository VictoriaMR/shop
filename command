#!/usr/bin/env php
<?php 
require './init.php';
define('IS_CLI', true);
if (!isset($argv[1])) exit('class error');
if (!isset($argv[2])) exit('function error');
$func = $argv[2];
\App::make($argv[1])->$func();
\App::runOver();