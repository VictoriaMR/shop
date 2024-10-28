<?php
@session_start();
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
define('ROOT_PATH', __DIR__.'/');
require './frame/Start.php';
\App::send();