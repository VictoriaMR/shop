<?php

namespace app\task\main;
use app\task\TaskDriver;

class Queue extends TaskDriver
{
	public $config = [
        'name' => '延时队列任务',
        'cron' => ['* * * * *'],
    ];

	public function __construct($process=[])
	{
		parent::__construct($process);
		$this->config['info'] = '延时队列任务';
	}

	public function run()
	{
		$service = service('Queue');
		if (!$service->count()) {
			//任务挂起
			$this->taskMonitor();
			return false;
		}
		$data = $service->getInfo();
		$func = $data['method'];
		$rst = make($data['class'])->$func($data['param']);
		if ($rst) {
			$service->pop();
		}
		return true;
	}
}