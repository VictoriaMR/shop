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
			$this->sleep = 1;
		}
		$this->config['info'] = '系统核心队列任务';
	}

	protected function before()
	{
		$files = getDirFile(__DIR__.DIRECTORY_SEPARATOR.'main');
		array_reverse($files);
		foreach ($files as $key => $value) {
			$className = __NAMESPACE__.str_replace([__DIR__, '.php'], '', $value);
			//重新缓存配置
			$class = make($className);
			if ($class) {
				$keyName = $this->tasker->getKeyByClassName($className);
				$config = $class->config;
				$config['lockTimeout'] = $class->lockTimeout;
				$config['runTimeLimit'] = $class->runTimeLimit;
				$config['classname'] = $className;
				$config['status'] = $this->getInfo('status', $keyName);
				$config['runAt'] = $this->getNextTimeByCronArray($config['cron']);
				redis(2)->sAdd(self::TASKPREFIX.'all', $keyName);
				//加入缓存
				foreach ($config as $k => $v) {
					$this->setInfo($k, $v, $keyName);
				}
				$this->taskList[$keyName] = $className;
			} else {
				$this->echo('类不存在', $className);
			}
		}
	}

	public function run()
	{
		echo '1'.PHP_EOL;
		foreach ($this->taskList as $k => $v){
			$info = $this->getInfo('', $k);
			if (($info['boot'] ?? '') !== 'off') { // 开始启动任务
				//运行时间
				if ($info['runAt'] <= time() && $this->locker->lock($k, $info['lockTimeout'])) {
					$cas = $this->locker->holdLock($k);
					echo $k.PHP_EOL;
					try {
						//下次运行时间
						$nextRunAt = $this->getNextTimeByCronArray($info['cron']);
						if ($nextRunAt < 0 || $nextRunAt === false) {
							unset($this->taskList[$k]);
							$this->echo('运行计划配置无效', $k);
						} else {
							$this->tasker->start($v, $info['lockTimeout'], $cas);
							$this->setInfo('nextRun', $nextRunAt > 0 ? date('Y-m-d H:i:s', $nextRunAt) : 'alwaysRun', $k);
							$this->setInfo('runAt', $nextRunAt, $k);
						}
					} catch (\Exception $e) {
						debug()->runlog($e->getLine().'-'.$e->getFile().'-'.$e->getMessage(), 'task_error');
					}
				}
			}
		}
		$this->echo("\n当前工作任务数：".count($this->taskList));
		return true;
	}
}