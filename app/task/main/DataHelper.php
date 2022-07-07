<?php

namespace app\task\main;
use app\task\TaskDriver;

class DataHelper extends TaskDriver
{
	public $config = [
        'name' => '数据入库任务',
        'cron' => ['* * * * *'],
    ];

	public function run()
	{
		$info = make('app/service/supplier/Url')->loadData(['status'=>0]);
		if (empty($info)) {
			return false;
		}
		//获取空闲机器
		$socketService = make('app/service/Socket');
		$list = $socketService->getAutoOnlineList();
		if (empty($list)) {
			sleep(5);
			return true;
		}
		foreach ($list as $value) {
			$socketService->pushToAuto($value, $info);
		}
		return true;
	}
}