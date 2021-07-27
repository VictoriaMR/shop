<?php
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
define('ROOT_PATH', strtr(dirname(__DIR__), '\\', '/').'/');
define('APP_TEMPLATE_TYPE', 'bag');
define('APP_STATIC', false);
define('APP_SITE_ID', 80);
define('TEMPLATE_SUFFIX', 'html');
require ROOT_PATH.'frame/Start.php';