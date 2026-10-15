<?php

namespace app\task\main;
use app\task\TaskDriver;

class DataHelper extends TaskDriver
{
	public static $config = [
		'name' => '产品维护任务',
		'type' => self::TYPE_OTHER,
		'cron' => [],
	];

	public function run()
	{
		$info = service('supplier/Url')->loadData(['status' => 0]);
		if (empty($info)) {
			return false;
		}
		// 获取空闲机器, 无在线机器时直接退出释放进程资源
		$socketService = service('Socket');
		$list = $socketService->getAutoOnlineList();
		if (empty($list)) {
			$this->echo('暂无空闲采集机器');
			return false;
		}
		foreach ($list as $value) {
			$socketService->pushToAuto($value, $info);
		}
		$this->updateLock();
		return true;
	}
}