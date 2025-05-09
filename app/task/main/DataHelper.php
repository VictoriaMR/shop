<?php

namespace app\task\main;
use app\task\TaskDriver;

class DataHelper extends TaskDriver
{
	public $config = [
		'name' => '产品维护任务',
		'cron' => ['* * * * *'],
	];

	public function run()
	{
		$info = service('supplier/Url')->loadData(['status'=>0]);
		if (empty($info)) {
			$this->taskMonitor();
			return false;
		}
		//获取空闲机器
		$socketService = service('Socket');
		$list = $socketService->getAutoOnlineList();
		if (empty($list)) {
			sleep(10);
			return true;
		}
		foreach ($list as $value) {
			$socketService->pushToAuto($value, $info);
		}
		return true;
	}
}