<?php
require './init.php';
define('IS_CLI', false);
@session_start();
\App::send();