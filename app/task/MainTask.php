<?php

namespace app\task;
use app\task\TaskDriver;

class MainTask extends TaskDriver
{
	public static $config = [
		'name' => '系统核心队列任务',
		'cron' => ['* * * * *'],
	];

	protected $taskCrons = [];

	protected $wait = 0; // 首次不休眠

	protected function beforeStart()
	{
		// 获取全部的任务
		$taskList = getDirFile(ROOT_PATH . 'app/task/main');
		foreach ($taskList as $filePath) {
			$classPath = strtr(str_replace([ROOT_PATH, '.php'], '', $filePath), '\\', '/');
			$info = $this->getCache($classPath);
			if (empty($info)) {
				// 仅autoload类文件, 读取静态属性, 不实例化
				\App::autoload($classPath);
				$className = strtr($classPath, '/', '\\');
				$info = $className::$config;
			}
			!isset($info['boot']) && $info['boot'] = 'on';
			!isset($info['status']) && $info['status'] = 'stop';
			$this->taskCrons[$classPath] = $info['cron'];
			$info['next_run'] = $this->getNextTime($info['cron']);
			if ($info['next_run']) {
				$this->listAdd($classPath, $info['next_run']);
			}
			$this->setCache($classPath, $info);
		}
	}

	protected function listAdd($classPath, $nextRun)
	{
		return $this->redis()->zAdd($this->getKey('delay'), $nextRun, $classPath);
	}

	/**
	 * 阻塞等待 → 检查到期任务 → 启动 → 计算下次等待
	 */
	public function run()
	{
		// 阻塞等待 (首次 wait=0 跳过)
		$this->waitForSignal($this->wait);

		$key = $this->getKey('delay');

		// 拉取所有到期任务并启动
		$tasks = $this->redis()->zRangeByScore($key, 0, time());
		foreach ($tasks as $classPath) {
			$this->startTask($classPath);
		}
		// 获取第一条未到期任务, 计算下次睡眠时间
		$next = $this->redis()->zRange($key, 0, 0, true);
		$this->wait = empty($next) ? 3600 : max(1, (int)current($next) - time());
		$this->wait = 50;
		return true;
	}

	protected function startTask($classPath)
	{
		$key = $this->getKey('delay');
		$nextRun = $this->getNextTime($this->taskCrons[$classPath]);
		if ($nextRun > 0) {
			$this->redis()->zAdd($key, $nextRun, $classPath);
		} else {
			$this->redis()->zRem($key, $classPath);
		}
		// 启动子进程
		// frame('Task')->start($classPath);
	}
}