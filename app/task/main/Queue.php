<?php

namespace app\task\main;
use app\task\TaskDriver;

class Queue extends TaskDriver
{
	public static $config = [
		'name' => '延时队列任务',
		'type' => self::TYPE_OTHER,
		'cron' => [],
	];

	public function run()
	{
		$service = service('tool/Queue');
		// 单次原子弹出, 避免 count + getInfo + pop 三次 Redis 往返开销及失败死循环
		$data = $service->pop();
		if (empty($data) || !is_array($data)) {
			return false;
		}
		$class = $data['class'] ?? '';
		$func = $data['method'] ?? '';
		if (empty($class) || empty($func)) {
			return true;
		}
		try {
			$rst = make($class)->$func($data['param'] ?? null);
			if (!$rst) {
				$service->dealFalse($data);
			}
		} catch (\Throwable $e) {
			$service->dealFalse($data);
			$this->echo('队列异常: ' . $e->getMessage());
		}
		$this->updateLock();
		return true;
	}
}