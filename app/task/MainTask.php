<?php

namespace app\task;

class MainTask extends TaskDriver
{
	private $taskList = [];

	public function __construct($process=[])
	{
		redis(2)->del(self::TASKPREFIX.'all');
		if (!empty($process)) {
			$this->lockTimeout = config('task.timeout');
			$this->runTimeLimit = 0;
			$this->sleep = 1;
		}
		$this->config['info'] = '系统核心队列任务';
		parent::__construct($process);
	}

	protected function before()
	{
		$files = getDirFile(__DIR__.DS.'main');
		$files = array_reverse($files);
		foreach ($files as $key => $value) {
			$className = strtr(__NAMESPACE__, '\\', DS).str_replace([__DIR__, '.php'], '', $value);
			//重新缓存配置
			$class = make($className);
			if ($class) {
				$keyName = $this->tasker->getKeyByClassName($className);
				$data = [];
				$data['boot'] = $this->getInfo('boot', $keyName) == 'off' ? 'off' : 'on';
				$config = $class->config;
				$data['info'] = $config['info'];
				$data['lockTimeout'] = $class->lockTimeout;
				$data['runTimeLimit'] = $class->runTimeLimit;
				$data['sleep'] = $class->sleep;
				$data['classname'] = $className;
				if ($data['boot'] == 'on') {
					$data['runAt'] = $this->getNextTimeByCronArray($config['cron']);
					$data['nextRun'] = $data['runAt'] > 0 ? now($data['runAt']) : 'alwaysRun';
				}
				redis(2)->sAdd(self::TASKPREFIX.'all', $keyName);
				$this->delInfo($keyName);
				//加入缓存
				$this->setInfoArray($data, $keyName);
				$this->taskList[$keyName] = array_merge($data, $config);
			} else {
				$this->echo('类不存在', $className);
			}
		}
	}

	public function run()
	{
		//获取当前工作状态
		$boot  = $this->getInfo('boot');
		if ($boot == 'off') {
			return false;
		}
		foreach ($this->taskList as $k => $v){
			$info = $this->getInfo('', $k);
			if (($info['boot'] ?? '') !== 'off') { // 开始启动任务
				//运行时间
				if ($info['runAt'] <= time() && $this->locker->lock($k, $info['lockTimeout'])) {
					$cas = $this->locker->holdLock($k);
					try {
						//下次运行时间
						$nextRunAt = $this->getNextTimeByCronArray($v['cron']);
						if ($nextRunAt < 0 || $nextRunAt === false) {
							unset($this->taskList[$k]);
							$this->echo('运行计划配置无效', $k);
						} else {
							$this->tasker->start($k, $info['lockTimeout'], $cas);
							$this->setInfo('nextRun', $nextRunAt > 0 ? now($nextRunAt) : 'alwaysRun', $k);
							$this->setInfo('runAt', $nextRunAt, $k);
						}
					} catch (\Exception $e) {
						debug()->runlog($e->getLine().'-'.$e->getFile().'-'.$e->getMessage(), 'task_error');
					}
				}
			}
		}
		$this->echo('当前工作任务数：'.count($this->taskList));
		return true;
	}
}