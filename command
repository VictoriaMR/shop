#!/usr/bin/env php
<?php
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
defined('IS_CLI') || define('IS_CLI', true);
require __DIR__ . '/init.php';
\App::make($argv[1])->{$argv[2]}();
\App::runOver();