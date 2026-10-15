<?php

namespace app\task\main;
use app\task\TaskDriver;

class CurrencyRate extends TaskDriver
{
	public static $config = [
		'name' => '货币汇率更新任务',
		'type' => self::TYPE_CRON,
		'cron' => ['0 8 * * *'],
	];

	public function run()
	{
		sys()->currency()->updateRate();
		return false;
	}
}