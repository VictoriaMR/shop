<?php
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
require __DIR__ . '/init.php';
\App::send();