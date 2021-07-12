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
		$files = getDirFile(__DIR__.DIRECTORY_SEPARATOR.'main');
		array_reverse($files);
		foreach ($files as $key => $value) {
			$className = __NAMESPACE__.str_replace([__DIR__, '.php'], '', $value);
			$class = make($className);
			if ($class) {
				$keyName = $this->tasker->getKeyByClassName($className);
				$config = $class->config;
				if (!isset($this->taskList[$keyName])) {
					$this->taskList[$keyName] = ['runAt'=>0, 'status'=>''];
					redis(2)->sAdd(self::TASKPREFIX.'all', $keyName);
				} else {
					$this->taskList[$keyName]['runAt'] = $this->getNextTimeByCronArray($config['cron']);
				}
				$this->taskList[$keyName]['lockTimeout'] = $class->lockTimeout;
				$this->taskList[$keyName]['runTimeLimit'] = $class->runTimeLimit;
				$this->taskList[$keyName]['cron'] = $config['cron'];
				$this->taskList[$keyName]['classname'] = $className;
				$this->taskList[$keyName]['status'] = $this->getInfo('boot', $className);
			} else {
				$this->echo('类不存在', $className);
			}
		}
	}

	public function run()
	{
		$min_sleep = 1;
		print_r($this->taskList);
		foreach ($this->taskList as $k => $v){
			// 更新任务状态
			$this->taskList[$k]['status'] = $v['status'] = $this->getInfo('boot', $k);
			if ($v['status'] !== 'off') { // 开始启动任务
				if ($v['runAt'] <= time()) {
					//获取锁成功则执行
					if ($this->locker->lock($k, $v['lockTimeout'])) {
						$cas = $this->locker->holdLock($k);
						try {
							//下次运行时间
							$runAt = $this->getNextTimeByCronArray($v['cron']);
							$this->taskList[$k]['runAt'] = $runAt;
							$this->tasker->start($v['classname'], $v['lockTimeout'], $cas);
							$this->setInfo('nextRun', $runAt > 0 ? date('Y-m-d H:i:s', $runAt) : 'alwaysRun', $k);

							if ($this->taskList[$k]['runAt'] < 0 || $this->taskList[$k]['runAt'] === false) {
								unset($this->taskList[$k]);
								$this->echo('运行计划配置无效', $k);
							}
						} catch (\Exception $e) {
							make('frame/Debug')->runlog($e->getLine().'-'.$e->getFile().'-'.$e->getMessage(), 'task_error');
						}
					}
				} else {
					if ($v['runAt'] - time() < $min_sleep) {
						$min_sleep = $v['runAt'] - time();
					}
				}
			}
		}
		$this->echo("\n当前工作任务数：".count($this->taskList));
		$this->sleep = $min_sleep;
		return true;
	}
}