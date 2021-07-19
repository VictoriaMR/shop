<?php
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
define('ROOT_PATH', strtr(dirname(__DIR__), '\\', '/').'/');
define('APP_TEMPLATE_TYPE', 'admin');
define('APP_STATIC', false);
define('APP_SITE_ID', 00);
ini_set('date.timezone', 'Asia/Shanghai');
require ROOT_PATH.'frame/Start.php';