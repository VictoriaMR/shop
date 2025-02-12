<?php

namespace app\task\main;
use app\task\TaskDriver;

class Product extends TaskDriver
{
	private $language = [];

	public $config = [
		'name' => '产品入库任务',
		'cron' => ['* * * * *'],
	];

	public function run()
	{
	}
}