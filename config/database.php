<?php
return [
	'default' => [
		'db_host'	  => env('DB_HOST', '127.0.0.1'), 	//地址
		'db_port'	  => env('DB_PORT', '3306'),        //端口
		'db_database' => env('DB_DATABASE', 'prettybag'), //数据库名称
		'db_username' => env('DB_USERNAME', 'root'),    //用户
		'db_password' => env('DB_PASSWORD', 'root'),  	//密码
		'db_charset'  => env('DB_CHARSET', 'utf8mb4'),  //字符集
	],
	'static' => [
		'db_host'	  => env('DB_HOST', '127.0.0.1'), 	//地址
		'db_port'	  => env('DB_PORT', '3306'),        //端口
		'db_database' => env('DB_DATABASE_STATIC'),     //数据库名称
		'db_username' => env('DB_USERNAME', 'root'),    //用户
		'db_password' => env('DB_PASSWORD', 'root'),  	//密码
		'db_charset'  => env('DB_CHARSET', 'utf8mb4'),  //字符集
	],
];