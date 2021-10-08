<?php

namespace app\task\main;
use app\task\TaskDriver;

class DataHelper extends TaskDriver
{
	public function __construct($process=[])
	{
		parent::__construct($process);
		if ($process !== false) {
			$this->lockTimeout = config('task.timeout');
			// 每运行6小时退出一次
			$this->runTimeLimit = 60*60*6;
		}
		$this->config['info'] = '数据数据任务';
		$this->config['cron'] = ['* * * * *']; //每天3点整运行
	}

	public function run()
	{
		$info = make('app/service/supplier/Url')->loadData(['status'=>0]);
		if (empty($info)) {
			$this->taskSleep(300);
			return false;
		}
		//获取空闲机器
		$socketService = make('app/service/Socket');
		$list = $socketService->getAutoOnlineList();
		foreach ($list as $value) {
			$socketService->pushToAuto($value, $info);
		}
		return true;
	}
}