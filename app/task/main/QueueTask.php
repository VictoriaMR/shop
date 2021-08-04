<?php

namespace app\task\main;
use app\task\TaskDriver;

class QueueTask extends TaskDriver
{
	public function __construct($process=[])
	{
		parent::__construct($process);
		if ($process !== false) {
			$this->lockTimeout = config('task.timeout');
			// 每运行6小时退出一次
			$this->runTimeLimit = 60*60*6;
		}
		$this->config['info'] = 'email';
		$this->config['cron'] = ['* * * * *']; //ayaways run
	}

	public function run()
	{
		$service = make('app/service/QueueService');
		if ($service->count()) {
			$data = $service->pop();
			$func = $data['method'];
			$rst = make($data['class'])->$func($data['param']);
			if ($rst !== true) {
				$data['queue_error'] = $rst;
				$service->dealFalse($data);
			}
		} else {
			$this->taskSleep(300);
			return false;
		}
	}
}