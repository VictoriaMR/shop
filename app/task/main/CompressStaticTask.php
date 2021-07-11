<?php

namespace app\task\main;
use app\task\TaskDriver;

class CompressStaticTask extends TaskDriver
{
	public function __construct($process=[])
	{
		parent::__construct($process);
		if ($process !== false) {
			$this->lockTimeout = config('task.timeout');
			// 每运行6小时退出一次
			$this->runTimeLimit = 60*60*6;
		}
		$this->config['info'] = '静态文件压缩进程';
		$this->config['cron'] = ['* * * * *']; //每天3点整运行

	}

	public function run()
	{

	}
}
