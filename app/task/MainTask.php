<?php

namespace app\task;

class MainTask extends TaskDriver
{
	private $taskList = [];

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

	protected function before()
	{
		$this->taskList = getDir(__DIR__.DIRECTORY_SEPARATOR.'main');
		foreach ($this->taskList as $key => $value) {
			$this->taskList[$key] = __NAMESPACE__.str_replace(__DIR__, '', $value);
		}
		array_reverse($this->taskList);
	}

	public function run()
	{
		return true;
	}
}