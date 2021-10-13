<?php

namespace app\task\main;
use app\task\TaskDriver;

class Queue extends TaskDriver
{
	public function __construct($process=[])
	{
		parent::__construct($process);
		if ($process !== false) {
			$this->lockTimeout = config('task.timeout');
			// 每运行6小时退出一次
			$this->runTimeLimit = 60*60*6;
		}
		$this->config['info'] = '延时队列任务';
		$this->config['cron'] = ['* * * * *']; //ayaways run
	}

	public function run()
	{
		$service = make('app/service/Queue');
		if ($service->count()) {
			$data = $service->getInfo();
			$func = $data['method'];
			$rst = make($data['class'])->$func($data['param']);
			if ($rst) {
				$service->pop();
			} else {
				$data['queue_error'] = $rst;
				$service->dealFalse($data);
			}
			return true;
		} else {
			$this->taskSleep(300);
			return false;
		}
	}
}