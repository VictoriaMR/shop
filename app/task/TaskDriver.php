<?php

namespace app\task;

abstract class TaskDriver
{
	const TASKPREFIX ='frame-task:';
	const TASKUDPSERVERKEY = 'frame-task:udp-block-server';
	protected $startTime;
	protected $isRealObject = true;
	protected $lock ='';
	protected $cas ='';
	protected $ip = '';
	protected $data = '';
	protected $key = '';
	protected $locker;
	protected $tasker;
	public $config = [
		'info' => '任务说明',
		'task_url' => '',
		'task_ip' => '',
		'cron' => [
			// 按下面格式配置， 可同时配置多条
			// * * * * * *	分 时 日 月 周 (全为*表示持续运行)
			// 0 3 * * * *”	数字精确配置, 星号为任意.(每天凌晨3点整)
			// 15,30 3 * * *	逗号表示枚举 (每天3点15分和3点30分)
			// 15-30 3 * * *	短线表示范围 (每天的3点15分到30分持续运行)
			// 0-30/10 3 * * *	斜杠表示间隔 (每天3点0分到30分之间, 每10分钟一次)
			// */10 5-8 * * *	斜杠表示间隔 (每天5-8点, 每10分钟一次)
		],
	];
	protected $lockTimeout = 600;
	protected $runCountLimit =-1;
	protected $runTimeLimit = 0;
	protected $sleep = 30;

	public function __construct($process=[])
	{
		if ($process===false) {
			$this->isRealObject = false;
		} else {
			set_time_limit(0);
			$process['lock'] = json_decode(base64_decode($process['lock']), true);
			$process['data'] = json_decode(base64_decode($process['data']), true);
			list($this->lock, $this->cas) = $process['lock'];
			if (isset($process['data'])) {
				$this->data = $process['data'];
				if (isset($process['data']['ip'])) {
					$this->ip = $process['data']['ip'];
				}
			}
			$this->startTime = time();
			$this->key = self::TASKPREFIX.$this->lock;
			// 设置任务当次启动时间
			$this->setInfo('start_time', now());
			$this->setInfo('ip', $this->ip);
			$this->setInfo('status', 'runing');
			$this->setInfo('process.pid', getmypid());
			$this->setInfo('process.uid', getmyuid());
			$this->setInfo('process.gid', getmygid());
			$this->setInfo('process.user', get_current_user());
			$this->locker = make('frame/Locker');
			$this->tasker = make('frame/Task');

			redis(2)->sAdd(self::TASKPREFIX.'all', $this->lock);
			redis(2)->hIncrBy($this->key, 'count', 1);
			redis(2)->hDel($this->key, 'loopCount');
			$this->startUp();
		}
	}

	public function setInfo($field, $value)
	{
		return redis(2)->hSet($this->key, $field, $value);
	}

	public function getInfo($field='')
	{
		$key = self::TASKPREFIX.$this->tasker->getKeyByClassName($this->lock);
		if(empty($field)){
			return redis(2)->hGetAll($key);
		} else {
			return redis(2)->hGet($key, $field);
		}
	}

	public function startUp()
	{
		return $this->setInfo('boot', 'on');
	}

	public function echo($msg)
	{
		$this->setInfo('info', $msg);
	}

	protected function before() {}
	protected function beforeShutdown() {}
	protected function beforeRestart() {}

	public function continueRuning()
	{
		if (!$this->updateLock()) {
			return false;
		}
		// 关闭的不运行, 主任务不能关闭
		$boot = $this->getInfo('boot');
		if($this->tasker->getKeyByClassName($this->lock) == 'app-task-MainTask' && $boot == 'off') {
			$boot = 'restart';
		}
		if ($boot == 'off') {
			$this->beforeShutdown();
			return false;
		}
		// 重启任务
		if($boot == 'restart'){
			$this->beforeRestart();
			$this->setInfo('boot','on');
			return false;
		}
		// 设定有限运行次数的
		if ($this->runCountLimit == 0) {
			return false;
		}
		if ( $this->runCountLimit > 0) {
			$this->runCountLimit--;
		}
		// 设置了运行时间限制的
		if ($this->runTimeLimit > 0 && time() - $this->startTime > $this->runTimeLimit) {
			return false;
		}
		return true;
	}

	protected function updateLock()
	{
		$this->ping();
		if ($this->locker->update($this->lock, $this->lockTimeout)) {
			return true;
		}
		return false;
	}

	protected function ping()
	{
		$this->setInfo('ping_time', now());
	}

	public function start()
	{
		if ($this->locker->getLock($this->lock, $this->cas)) {
			$this->echo('任务启动中 '.now());
			$this->before();
			$result = true;
			$runtime = time();
			while ($result && $this->continueRuning()) {
				echo '111'.PHP_EOL;
				redis(2)->hIncrBy($this->key, 'loopCount', 1);
				$result = $this->run();
				$usgaMem = memory_get_usage();
				$this->setInfo('memoryUsage', get1024Peck($usgaMem - APP_MEMORY_START).'/'.get1024Peck($usgaMem));
				if($result) {
					// 防止死循环减轻服务器压力
					if (time() - $runtime < 1 && $this->sleep < 1) {
						sleep($this->sleep);
					}
					if ($this->sleep >= 1) {
						sleep($this->sleep);
					}
					$runtime = time();
				}
			}
			$this->locker->unlock($this->lock);
			$this->echo('任务已退出 '.now());
        }
	}

	 function get1024Peck($dec = 2)
	{
		$size = memory_get_usage() - APP_MEMORY_START;
		$a = ['B', 'KB', 'MB', 'GB', 'TB'];
		$pos = 0;
		while ($size >= 1024) {
			$size /= 1024;
			$pos++;
		}
		return round($size, $dec).' '.$a[$pos];
	}

    // 任务类具体工作方法, 单次工作， 外层已有循环
    // 如果 run 方法单次运行比较久， 请务必注意适当时间调用 $this->lockUpdate(); 时间间隔不能大于 $this->lockTimeout
    abstract public function run();
}