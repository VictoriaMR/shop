<?php

namespace app\task;

class MainTask extends TaskDriver
{
	public function __construct($process=[])
	{
		parent::__construct($process);
		if (!empty($process)) {
			$this->lockTimeout = config('task.timeout');
			$this->runTimeLimit = 0;
			$this->sleep = 60;
		}
		$this->config['info'] = '系统核心队列任务';
	}

	public function run()
	{
		echo __FUNCTION__;
	}
}