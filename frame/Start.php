<?php
@ini_set('session.cookie_httponly', 1);
@session_start();
require ROOT_PATH.'frame'.DS.'App.php';
require ROOT_PATH.'frame'.DS.'Helper.php';
is_file(ROOT_PATH.'vendor'.DS.'autoload.php') && require ROOT_PATH.'vendor'.DS.'autoload.php';
\App::init();