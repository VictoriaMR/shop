<?php
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
define('DS', '/');
define('ROOT_PATH', strtr(__DIR__, '\\', '/').DS);
define('APP_PATH', ROOT_PATH.'app'.DS);
require ROOT_PATH.'frame'.DS.'Start.php';
\App::send();