<?php 
return [
	'lmr.bag.cn' => [
		'type' => 'home',
		'cache' => false,
		'view_suffix' => 'html',
		'path' => 'newsme',
		'name' => 'Clothes',
		'site_id' => '80'
	],
	'lmr.upload.cn' => [
		'type' => 'home',
		'cache' => true,
		'view_suffix' => 'html',
		'path' => 'storage',
		'name' => '',
		'site_id' => '11'
	],
	'lmr.admin.cn' => [
		'debug' => true,
		'type' => 'admin',
		'cache' => false,
		'view_suffix' => '',
		'path' => 'admin',
		'name' => '管理后台',
		'site_id' => '10'
	],
];