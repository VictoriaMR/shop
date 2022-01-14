<?php
return [
	'default' => [
		'db_host'	  => config('env', 'DB_HOST', '127.0.0.1'),
		'db_port'	  => config('env', 'DB_PORT', '3306'),
		'db_database' => config('env', 'DB_DATABASE', 'shop'),
		'db_username' => config('env', 'DB_USERNAME', 'root'),
		'db_password' => config('env', 'DB_PASSWORD', 'root'),
		'db_charset'  => config('env', 'DB_CHARSET', 'utf8mb4'),
	],
	'static' => [
		'db_host'	  => config('env', 'DB_HOST', '127.0.0.1'),
		'db_port'	  => config('env', 'DB_PORT', '3306'),
		'db_database' => config('env', 'DB_DATABASE_STATIC', 'shop_static'),
		'db_username' => config('env', 'DB_USERNAME', 'root'),
		'db_password' => config('env', 'DB_PASSWORD', 'root'),
		'db_charset'  => config('env', 'DB_CHARSET', 'utf8mb4'),
	],
];