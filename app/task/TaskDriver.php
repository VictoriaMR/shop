<?php

namespace app\task;

abstract class TaskDriver
{
	public static $config = [];
	protected $task_info = [];

	const TASKPREFIX ='frame:task:';

	protected $timeout = 600;

	protected function beforeStart(){}

	protected function beforeStop(){}

	// 任务启动, 不判断其他参数, 直接启动
	public function start()
	{
		$key = strtr(get_class($this), '\\', '/');
		$this->task_info = $this->getCache($key);
		if (empty($this->task_info)) {
			$this->task_info = static::$config;
			$this->task_info['boot'] = 'on';
			$this->task_info['start_count'] = 0;
		} else {
			if ($this->task_info['boot'] === 'off') {
				return false;
			}
			// 启动中的任务[防止重复启动]
			if ($this->task_info['status'] === 'running' && $this->task_info['run_at'] + $this->timeout > time()) {
				return false;
			}
		}
		$this->task_info['run_count'] = 0;
		$this->task_info['status'] = 'running';
		$this->task_info['start_at'] = time();
		$this->task_info['start_count']++;
		$this->task_info['process_pid'] = getmypid();
		$this->task_info['process_user'] = get_current_user();
		// 启动前准备
		$this->beforeStart();
		// 任务启动
		while (true) {
			$rst = $this->run();
			$this->task_info['run_count']++;
			$this->task_info['run_at'] = time();
			$this->task_info['memory_usage'] = memory_get_usage() - APP_MEMORY_START;
			$this->setCache($key, $this->task_info);
			if ($rst === false) {
				break;
			}
		}
		// 任务结束
		$this->beforeStop();
		$this->task_info['status'] = 'stop';
		$nextRun = $this->getNextTime(static::$config['cron']);
		$this->task_info['run_next'] = $nextRun;
		$this->setCache($key, $this->task_info);
		// 重新入队延时队列, 等待 MainTask 下次调度
		if ($nextRun > 0) {
			$this->redis()->zAdd($this->getKey('delay'), $nextRun, $key);
		}
		return true;
	}

	/**
	 * 更新运行锁 - 防止长时间处理的任务被 MainTask 误判为超时
	 */
	protected function updateLock()
	{
		$key = get_class($this);
		$info = $this->getCache($key);
		$info['run_at'] = time();
		$this->setCache($key, $info);
		return true;
	}

	public function getNextTime($cron)
	{
		$result=false;
		foreach ($cron as $val){
			$v=$this->getNextTimeByCron($val);
			if (is_bool($result) || $v<$result) {
				$result=$v;
			}
		}
		return $result ?: 0;
	}

	/**
	 * 在读取corn配置是个做基本检查和过滤， 包括：格式， 运行的字符，等， 传过来的必须是合规的字串
	 * 按下面格式配置， 可同时配置多条, 日与周同时配置， 忽略周配置
	 * * * * *	分 时 日 月 周 (全为*表示持续运行)
	 * 0 3 * * *	数字精确配置, 星号为任意.(每天凌晨3点整)
	 * 15,30 3 * * *	逗号表示枚举 (每天3点15分和3点30分)
	 * 15-30 3 * * *	短线表示范围 (每天的3点15分到30分持续运行)
	 * 0-30/10 3 * * *	斜杠表示间隔 (每天3点0分到30分之间, 每10分钟一次)
	 * *\/10 5-8 * * *	斜杠表示间隔 (每天5-8点, 每10分钟一次)
	 * 获取类似linux crontab格式单条配置的下一次运行时间
	 * @param  [type] $cornStr
	 * @author LiaoMr
	 * @DateTime 2026-04-24 15:12
	 */ 
	protected function getNextTimeByCron($cornStr)
	{
		if ($cornStr === '* * * * *') {
			return 0;
		}
		$arr = explode(' ', $cornStr);

		// 一次性获取当前时间各分量, 避免多次 date() 调用
		$ts = time();
		$nowMinute = (int)date('i', $ts);
		$nowHour   = (int)date('H', $ts);
		$nowDay    = (int)date('d', $ts);
		$nowMonth  = (int)date('m', $ts);
		$nowWeek   = (int)date('w', $ts);
		$nowYear   = (int)date('Y', $ts);
		$daysInMonth = (int)date('t', $ts);

		// 直接算出有序结果集, 不再传入完整 range 数组
		$minuteRange = $this->cronUnitParse($arr[0], 0, 59);
		$hourRange   = $this->cronUnitParse($arr[1], 0, 23);
		$dayRange    = $this->cronUnitParse($arr[2], 1, $daysInMonth);
		$monthRange  = $this->cronUnitParse($arr[3], 1, 12);
		$weekRange   = $this->cronUnitParse($arr[4], 0, 6);

		$year = $nowYear;

		// 取值: 从当前分量+1开始找下一个匹配值, 溢出则进位
		$minute = $this->cronNextVal($minuteRange, $nowMinute + 1);
		$step = 0;
		if ($minute < 0) {
			$minute = $minuteRange[0];
			$step = 1;
		}
		$hour = $this->cronNextVal($hourRange, $nowHour + $step);
		$step = 0;
		if ($hour < 0) {
			$hour = $hourRange[0];
			$step = 1;
		}
		if ($arr[4] == '*' || $arr[3] != '*') { // 按日参数计算
			$day = $this->cronNextVal($dayRange, $nowDay + $step);
			$step = 0;
			if ($day < 0) {
				$day = $dayRange[0];
				$step = 1;
			}
			$month = $this->cronNextVal($monthRange, $nowMonth + $step);
			if ($month < 0) {
				$month = $monthRange[0];
				$year++;
			}
		} else { // 按周参数计算
			$week = $this->cronNextVal($weekRange, $nowWeek + $step);
			$basetime = $ts;
			if ($week < 0) {
				$week = $weekRange[0];
				$basetime += (7 - $nowWeek + $week) * 86400;
			}
			$basemonth = (int)date('m', $basetime);
			$month = $this->cronNextVal($monthRange, $basemonth);
			if ($month < 0 || $basemonth != $month) {
				if ($month < 0) {
					$month = $monthRange[0];
					$year++;
				}
				$basetime = strtotime($year . '-' . $month . '-1 00:00:01');
				for ($i = 0; $i < 7; $i++) {
					if ((int)date('w', $basetime + $i * 86400) == $week) {
						$basetime += $i * 86400;
						break;
					}
				}
				$day = (int)date('d', $basetime);
			} else {
				$day = (int)date('d', $basetime);
			}
		}
		// 高位进位时, 低位归零
		if ($year != $nowYear) {
			$month  = $monthRange[0];
			$day    = $dayRange[0];
			$hour   = $hourRange[0];
			$minute = $minuteRange[0];
		}
		if ($month != $nowMonth) {
			$day    = $dayRange[0];
			$hour   = $hourRange[0];
			$minute = $minuteRange[0];
		}
		if ($day != $nowDay) {
			$hour   = $hourRange[0];
			$minute = $minuteRange[0];
		}
		if ($hour != $nowHour) {
			$minute = $minuteRange[0];
		}
		return mktime($hour, $minute, 0, $month, $day, $year);
	}

	/**
	 * 解析 cron 单个字段, 直接生成有序结果集
	 * @param string $unit cron 字段值 (如 "*\/10", "1,5,15", "3-7", "*")
	 * @param int $min 允许最小值
	 * @param int $max 允许最大值
	 * @return int[] 有序的匹配值数组
	 */
	protected function cronUnitParse($unit, $min, $max)
	{
		$step = 1;
		$str = $unit;
		// 提取步长
		if (($pos = strpos($str, '/')) !== false) {
			$step = max(1, (int)substr($str, $pos + 1));
			$str = substr($str, 0, $pos);
		}
		if ($str === '*') {
			// 全范围 + 步长: 直接用算术生成, 无需 range()
			$result = [];
			for ($v = $min; $v <= $max; $v += $step) {
				$result[] = $v;
			}
			return $result;
		}
		// 解析枚举和范围
		$values = [];
		foreach (explode(',', $str) as $part) {
			if (($dashPos = strpos($part, '-')) !== false) {
				$lo = (int)substr($part, 0, $dashPos);
				$hi = (int)substr($part, $dashPos + 1);
				for ($v = $lo; $v <= $hi; $v++) {
					$values[] = $v;
				}
			} else {
				$values[] = (int)$part;
			}
		}
		// 有步长时按索引跳步
		if ($step > 1) {
			sort($values);
			$result = [];
			for ($i = 0, $cnt = count($values); $i < $cnt; $i += $step) {
				$result[] = $values[$i];
			}
			return $result;
		}
		sort($values);
		return $values;
	}

	/**
	 * 在有序数组中找 >= $val 的最小值 (二分查找)
	 * @param int[] $range 有序数组
	 * @param int $val 目标值
	 * @return int 找到的值, 不存在返回 -1
	 */
	protected function cronNextVal($range, $val)
	{
		$cnt = count($range);
		if ($cnt === 0 || $val > $range[$cnt - 1]) {
			return -1;
		}
		if ($val <= $range[0]) {
			return $range[0];
		}
		// 二分查找
		$lo = 0;
		$hi = $cnt - 1;
		while ($lo < $hi) {
			$mid = ($lo + $hi) >> 1;
			if ($range[$mid] < $val) {
				$lo = $mid + 1;
			} else {
				$hi = $mid;
			}
		}
		return $range[$lo];
	}

	protected function echo($info)
	{
		$this->task_info['remark'] = $info;
	}

	/**
	 * 阻塞等待信号唤醒或超时
	 * @param int $timeout 最大等待秒数
	 */
	protected function waitForSignal($timeout)
	{
		if ($timeout <= 0) return;
		$key = $this->getKey('signal');
		$end = time() + $timeout;
		// 分段 blPop, 避免超过 Redis read_timeout
		while (($remain = $end - time()) > 0) {
			$wait = min($remain, 55);
			$result = $this->redis()->blPop($key, $wait);
			if ($result) return; // 被信号唤醒
		}
	}

	abstract protected function run();

	protected function getCache($key, $type='list')
	{
		return $this->redis()->hGet($this->getKey($type), $key);
	}

	protected function setCache($key, $value, $type='list')
	{
		return $this->redis()->hSet($this->getKey($type), $key, $value);
	}

	protected function getKey($type='list')
	{
		return self::TASKPREFIX.$type;
	}

	protected function redis($db=2)
	{
		return redis($db);
	}
}