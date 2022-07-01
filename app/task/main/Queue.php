<?php

namespace app\task\main;
use app\task\TaskDriver;

class Queue extends TaskDriver
{
	public $config = [
        'info' => '延时队列任务',
        'cron' => ['* * * * *'],
    ];

	public function __construct($process=[])
	{
		parent::__construct($process);
		$this->config['info'] = '延时队列任务';
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
			}
		} else {
			$this->taskSleep(500);
		}
		return true;
	}
}