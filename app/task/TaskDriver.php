<?php

namespace app\task;

abstract class TaskDriver
{
	const TASKPREFIX ='frame-task:';
	protected $startTime;
	protected $lock ='';
	protected $cas ='';
	protected $data = '';
	protected $locker;
	protected $tasker;
	public $config = [
		'info' => '任务说明',
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
	protected $runCountLimit = -1;
	protected $runTimeLimit = 0;
	protected $sleep = 30;

	public function __construct($process=[])
	{
		if (empty($process)) {
			$this->isRealObject = false;
		} else {
			set_time_limit(0);
			$process['lock'] = json_decode(base64_decode($process['lock']), true);
			list($this->lock, $this->cas) = $process['lock'];
			$this->startTime = time();
			// 设置任务当次启动时间
			$this->setInfo('info', $this->config['info']);
			$this->setInfo('lockTimeout', $this->lockTimeout);
			$this->setInfo('runTimeLimit', $this->runTimeLimit);
			$this->setInfo('sleep', $this->sleep);
			$this->setInfo('startTime', now());
			$this->setInfo('status', 'runing');
			$this->setInfo('process.pid', getmypid());
			$this->setInfo('process.uid', getmyuid());
			$this->setInfo('process.gid', getmygid());
			$this->setInfo('process.user', get_current_user());
			$this->locker = make('frame/Locker');
			$this->tasker = make('frame/Task');

			redis(2)->sAdd(self::TASKPREFIX.'all', $this->lock);
			redis(2)->hIncrBy(self::TASKPREFIX.$this->lock, 'count', 1);
			redis(2)->hDel(self::TASKPREFIX.$this->lock, 'loopCount');
			$this->startUp();
		}
	}

	protected function getKey($key)
	{
		return self::TASKPREFIX.$key;
	}

	protected function setInfo($field, $value, $key='')
	{
		if (empty($key)) {
			$key = $this->lock;
		}
		$key = $this->getKey($key);
		return redis(2)->hSet($key, $field, $value);
	}

	protected function getInfo($field='', $key='')
	{
		if (empty($key)) {
			$key = $this->lock;
		}
		$key = $this->getKey($key);
		if(empty($field)){
			return redis(2)->hGetAll($key);
		} else {
			return redis(2)->hGet($key, $field);
		}
	}

	protected function startUp($name='')
	{
		return $this->setInfo('boot', 'on', $name);
	}

	protected function echo($msg, $name='')
	{
		$this->setInfo('remark', $msg, $name);
	}

	protected function before() {}
	protected function beforeShutdown() {}
	protected function beforeRestart() {}

	protected function continueRuning()
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
				redis(2)->hIncrBy($this->getKey($this->lock), 'loopCount', 1);
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

	protected function cronUnitParse($unit, $allowRange)
	{
		if ($unit == '*') {
			$range = $allowRange;
			$step = 1;
		} else {
			$step = 1;
			$str = $unit;
			if (strpos($str, '/')) {
				list($str, $step) = explode('/', $str);
			}
			if ($str == '*') {
				$range = $allowRange;
			} else {
				$range = [];
				$str = explode(',', $str);
				foreach ($str as $val) {
					if (strpos($val, '-')) {
						$tmp = explode('-', $val);
						$range = array_merge($range, range($tmp[0], $tmp[1]));
					} else {
						$range[] = intval($val);
					}
				}
			}
		}
		sort($range);
		if ($step < 1) {
			$step = 1;
		}
		$i = 0;
		$result = [];
		while (isset($range[$i])) {
			$result[] = $range[$i];
			$i = $i + $step;
		}
		return $result;
	}

	protected function cronNextVal($range, $val)
	{
		reset($range);
		foreach ($range as $v) {
			if($v >= $val){
				return $v;
			}
		}
		return -1;
	}

	protected function getNextTimeByCron($corn)
	{
		$corn = trim(preg_replace('/\s+/', ' ', $corn));
		if ($corn == '* * * * *') {
			return 0;
		}
		$corn = explode(' ', $corn);
		$now = explode('-', date('i-H-d-m-w'));
		//确定取值范围
		$year = date('Y');
		$minuteRange = $this->cronUnitParse($corn[0], range(0, 59));
		$hourRange = $this->cronUnitParse($corn[1], range(0, 23));
		$dayRange = $this->cronUnitParse($corn[2], range(1, date('t')));
		$monthRange = $this->cronUnitParse($corn[3], range(1, 12));
		$weekRange = $this->cronUnitParse($corn[4], range(0, 6));
		//取值
		$minute = $this->cronNextVal($minuteRange, $now[0] + 1);
		$step = 0;
		if ($minute < 0) {
			$minute = $minuteRange[0];
			$step = 1;
		}
		$hour = $this->cronNextVal($hourRange, $now[1] + $step);
		$step = 0;
		if ($hour < 0) {
			$hour=$hourRange[0];
			$step = 1;
		}
		if ($corn[4] == '*' || $corn[3] != '*') {
			$day = $this->cronNextVal($dayRange, $now[2] + $step);
			$step = 0;
			if ($day < 0) {
				$day = $dayRange[0];
				$step = 1;
			}
			$month = $this->cronNextVal($monthRange, $now[3] + $step);
			if ($month < 0) {
				$month = $monthRange[0];
				$year++;
			}
		} else { // 按周参数计算
			$week = $this->cronNextVal($weekRange, $now[4]+$step);
			$basetime = time();
			if ($week < 0) {
				$week = $weekRange[0];
				$basetime = $basetime + (7 - date('w',$basetime) + $week)*24*60*60; // 基础时间递增一周
			}
			$basemonth = date('m',$basetime);
			$month = $this->cronNextVal($monthRange, $basemonth);
			if ($month < 0 || date('m', $basetime) != $month) {
				if ($month < 0) {
					$month = $monthRange[0];
					$year++;
				}
				$basetime = strtotime($year.'-'.$month.'-1 00:00:01');
				for ($i=0; $i<7; $i++) {
					$basetime = $basetime + $i*24*3600;
					$calweek = date('w', $basetime);
					if ($calweek == $week) {
						break;
					}
				}
				$day = date('d',$basetime);
			} else {
				$day = date('d',$basetime);
			}
		}
		if ($year != date('Y')) {
			$month = $monthRange[0];
			$day = $dayRange[0];
			$hour = $hourRange[0];
			$minute = $minuteRange[0];
		}
		if ($month != date('m')) {
			$day = $dayRange[0];
			$hour = $hourRange[0];
			$minute = $minuteRange[0];
		}
		if ($day != date('d')) {
			$hour = $hourRange[0];
			$minute = $minuteRange[0];
		}
		if ($hour != date('H')) {
			$minute = $minuteRange[0];
		}
		$result = mktime($hour, $minute, 0, $month, $day, $year);
		return $result > 0 ? $result : false;
	}

	protected function getNextTimeByCronArray($cornArray)
	{
		$result = false;
		foreach ($cornArray as $val) {
			$v = $this->getNextTimeByCron($val);
			if ($v === false) {
				return false;
			}
			$result === false && $result = $v;
			if ($v < $result) {
				$result = $v;
			}
		}
		return $result;
	}

	protected function taskSleep($time)
	{
		return redis(2)->hIncrBy(self::TASKPREFIX.$this->lock, 'runAt', $time);
	}

	abstract public function run();
}