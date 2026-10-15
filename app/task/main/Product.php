<?php

namespace app\task\main;
use app\task\TaskDriver;

class Product extends TaskDriver
{
	public static $config = [
		'name' => '产品入库任务',
		'type' => self::TYPE_OTHER,
		'cron' => [],
	];

	public function run()
	{
		$rst = purchase()->spu()->operateSpu();
		if ($rst) {
			$this->updateLock();
		}
		return (bool)$rst;
	}
}