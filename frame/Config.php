<?php
foreach (scandir(ROOT_PATH . 'config/') as $value) {
	if ($value == '.' || $value == '..') continue;
	$GLOBALS[str_replace('.php', '', $value)] = require ROOT_PATH . 'config' . DS . $value;
}