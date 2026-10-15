<?php

namespace app\task;

abstract class TaskDriver
{
	const TASKPREFIX = 'frame:task:';
	const LIST_KEY   = 'frame:task:list';
	const DELAY_KEY  = 'frame:task:delay';
	const SIGNAL_KEY = 'frame:task:signal';
	const MAIN_TASK  = 'app/task/MainTask';

	const TYPE_MAIN  = 'main';  // 常驻主任务
	const TYPE_CRON  = 'cron';  // 定时子任务
	const TYPE_OTHER = 'other'; // 其他/触发式子任务

	/** @var array */
	public static $config = [];

	/** @var array */
	protected $task_info = [];

	/** @var int 任务超时时间(秒) */
	protected $timeout = 600;

	/** @var bool 是否收到退出信号(Linux SIGTERM/SIGINT 或 Windows Ctrl+C) */
	protected $shouldStop = false;

	/** @var bool 任务是否已完成清理(配合 register_shutdown_function 兜底致命错误) */
	private $isFinished = true;

	/** @var int 上次同步状态到Redis的时间戳(减少高频循环写入压力) */
	private $lastSyncAt = 0;

	public function __construct()
	{
		$this->timeout = (int)config('task', 'timeout', 600);
	}

	protected function beforeStart() {}

	protected function beforeStop() {}

	abstract protected function run();

	/**
	 * 统一规范化任务键名 (统一为 app/task/... 格式)
	 */
	public static function formatKey(string $key): string
	{
		$key = trim(strtr($key, '\\', '/'), '/');
		if (strpos($key, 'app-task-main-') === 0) {
			return 'app/task/main/' . substr($key, 14);
		}
		if ($key === 'app-task-MainTask') {
			return self::MAIN_TASK;
		}
		return $key;
	}

	/**
	 * 获取任务类型: main(常驻主任务) | cron(定时任务) | other(其他任务)
	 */
	public static function resolveType(array $config, string $key = ''): string
	{
		if ($key === self::MAIN_TASK || ($config['type'] ?? '') === self::TYPE_MAIN) {
			return self::TYPE_MAIN;
		}
		if (!empty($config['type']) && in_array($config['type'], [self::TYPE_CRON, self::TYPE_OTHER], true)) {
			return $config['type'];
		}
		$crons = $config['cron'] ?? [];
		if (!empty($crons) && $crons !== ['* * * * *']) {
			return self::TYPE_CRON;
		}
		return self::TYPE_OTHER;
	}

	/**
	 * 检测任务进程是否真实存活 (兼容 Linux 内核级 PID 检测与 Windows 超时检测)
	 */
	public static function isProcessRunning(array $info, int $timeout = 600): bool
	{
		if (($info['status'] ?? '') !== 'running') {
			return false;
		}
		if (((int)($info['run_at'] ?? 0) + $timeout) <= time()) {
			return false;
		}
		// Linux 环境下通过 /proc 或 posix_kill 零开销校验 PID 是否真实存在
		if (!isWin()) {
			$pid = (int)($info['process_pid'] ?? 0);
			if ($pid > 0) {
				if (is_dir('/proc/' . $pid)) {
					return true;
				}
				if (function_exists('posix_kill')) {
					return @posix_kill($pid, 0);
				}
			}
		}
		return true;
	}

	/**
	 * 注册跨平台信号处理 (Linux pcntl_signal + Windows Ctrl+C)
	 */
	protected function registerSignals(): void
	{
		if (!isWin() && function_exists('pcntl_signal')) {
			if (function_exists('pcntl_async_signals')) {
				pcntl_async_signals(true);
			}
			// 忽略子进程退出信号, 防止 Linux 下产生僵尸进程 <defunct>
			if (defined('SIGCHLD') && defined('SIG_IGN')) {
				@pcntl_signal(SIGCHLD, SIG_IGN);
			}
			$handler = function () {
				$this->shouldStop = true;
			};
			if (defined('SIGTERM')) {
				@pcntl_signal(SIGTERM, $handler);
			}
			if (defined('SIGINT')) {
				@pcntl_signal(SIGINT, $handler);
			}
			if (defined('SIGHUP')) {
				@pcntl_signal(SIGHUP, $handler);
			}
		} elseif (isWin() && function_exists('sapi_windows_set_ctrl_handler')) {
			@sapi_windows_set_ctrl_handler(function () {
				$this->shouldStop = true;
			});
		}
	}

	/**
	 * 当前任务是否为常驻主进程
	 */
	protected function isMainTask(): bool
	{
		return static::formatKey(get_class($this)) === self::MAIN_TASK;
	}

	/**
	 * 当前任务是否为定时任务
	 */
	protected function isCronTask(): bool
	{
		$key = static::formatKey(get_class($this));
		return static::resolveType(static::$config, $key) === self::TYPE_CRON;
	}

	/**
	 * 任务启动入口
	 */
	public function start()
	{
		$key = static::formatKey(get_class($this));
		$now = time();
		$cached = $this->getCache($key);
		$config = static::$config;
		$type = static::resolveType($config, $key);

		if (empty($cached) || !is_array($cached)) {
			$this->task_info = array_merge($config, [
				'type'        => $type,
				'boot'        => 'on',
				'status'      => 'stop',
				'start_count' => 0,
				'run_count'   => 0,
				'next_run'    => 0,
			]);
		} else {
			$this->task_info = array_merge($cached, [
				'name' => $config['name'] ?? ($cached['name'] ?? $key),
				'type' => $type,
				'cron' => $config['cron'] ?? ($cached['cron'] ?? []),
			]);
			$boot = $this->task_info['boot'] ?? 'off';
			if ($boot === 'off' || $boot === 'offing') {
				$this->task_info['boot'] = 'off';
				$this->task_info['status'] = 'stop';
				$this->task_info['process_pid'] = 0;
				$this->task_info['next_run'] = 0;
				$this->setCache($key, $this->task_info);
				$this->redis()->zRem(self::DELAY_KEY, $key);
				return false;
			}
			// 防止同一任务重复并发启动 (支持 Linux 实时 PID 检测)
			if (self::isProcessRunning($this->task_info, $this->timeout)) {
				return false;
			}
		}

		// 设置进程标题, 方便 Linux ps/htop 运维查看
		if (function_exists('cli_set_process_title')) {
			@cli_set_process_title('shop-task: ' . $key);
		}

		// 注册跨平台优雅退出信号与致命错误兜底回调
		$this->shouldStop = false;
		$this->isFinished = false;
		$this->registerSignals();
		register_shutdown_function(function () use ($key) {
			if (!$this->isFinished) {
				$this->finishTask($key);
			}
		});

		// 子任务启动时先移除待触发标记, 防止执行期间被 MainTask 重复拉起
		if (!$this->isMainTask()) {
			$this->redis()->zRem(self::DELAY_KEY, $key);
		}

		$this->task_info['boot']         = 'on';
		$this->task_info['status']       = 'running';
		$this->task_info['start_at']     = $now;
		$this->task_info['run_at']       = $now;
		$this->task_info['start_count']  = (int)($this->task_info['start_count'] ?? 0) + 1;
		$this->task_info['run_count']    = 0;
		$this->task_info['process_pid']  = getmypid();
		$this->task_info['process_user'] = get_current_user();
		$this->task_info['memory_usage'] = memory_get_usage() - APP_MEMORY_START;
		$this->setCache($key, $this->task_info);
		$this->lastSyncAt = $now;

		try {
			$this->beforeStart();

			while (!$this->shouldStop) {
				$rst = $this->run();
				$now = time();
				$this->task_info['run_count']++;
				$this->task_info['run_at'] = $now;
				$this->task_info['memory_usage'] = memory_get_usage() - APP_MEMORY_START;

				if ($rst === false || $this->shouldStop) {
					break;
				}

				// 定期回收循环引用垃圾, 保障常驻/长循环任务内存稳定
				if (($this->task_info['run_count'] % 50) === 0) {
					gc_collect_cycles();
				}

				// 节流同步状态并检查后台停止指令 (主任务每轮检查, 子任务循环至少间隔2秒访问一次Redis)
				if ($this->isMainTask() || ($now - $this->lastSyncAt) >= 2) {
					$latest = $this->getCache($key);
					if (is_array($latest)) {
						$latestBoot = $latest['boot'] ?? 'on';
						if ($latestBoot === 'off' || $latestBoot === 'offing') {
							$this->task_info['boot'] = 'off';
							break;
						}
					}
					$this->setCache($key, $this->task_info);
					$this->lastSyncAt = $now;
				}
			}
		} catch (\Throwable $e) {
			$this->echo('执行异常: ' . $e->getMessage());
			frame('Debug')->runlog($key . ' Error: ' . $e->getMessage(), 'task_error');
		} finally {
			$this->beforeStop();
			$this->finishTask($key);
		}

		return true;
	}

	/**
	 * 任务结束清理并更新下次调度标记
	 */
	protected function finishTask(string $key)
	{
		if ($this->isFinished) {
			return;
		}
		$this->isFinished = true;

		$now = time();
		$latest = $this->getCache($key);
		if (is_array($latest) && in_array($latest['boot'] ?? '', ['off', 'offing'], true)) {
			$this->task_info['boot'] = 'off';
		}

		$this->task_info['status']       = 'stop';
		$this->task_info['run_at']       = $now;
		$this->task_info['process_pid']  = 0;
		$this->task_info['memory_usage'] = memory_get_usage() - APP_MEMORY_START;

		// 子任务执行完毕后: 若处于开启状态且为定时任务, 计算下次运行时间并更新 MainTask 调度标记
		if (($this->task_info['boot'] ?? 'off') === 'on' && $this->isCronTask()) {
			$nextRun = $this->getNextTime(static::$config['cron'] ?? []);
			$this->task_info['next_run'] = $nextRun;
			if ($nextRun > 0) {
				$this->redis()->zAdd(self::DELAY_KEY, $nextRun, $key);
				$this->signal();
			} else {
				$this->redis()->zRem(self::DELAY_KEY, $key);
			}
		} else {
			$this->task_info['next_run'] = 0;
			$this->redis()->zRem(self::DELAY_KEY, $key);
		}

		unset($this->task_info['run_next']);
		$this->setCache($key, $this->task_info);
	}

	/**
	 * 更新运行锁 - 防止长耗时任务被误判为超时, 同时支持感知后台停止信号
	 */
	protected function updateLock(): bool
	{
		$now = time();
		// 5秒内不重复刷写 Redis, 减轻服务器负担
		if (($now - $this->lastSyncAt) < 5) {
			return !$this->shouldStop;
		}
		$key = static::formatKey(get_class($this));
		$info = $this->getCache($key);
		if (is_array($info)) {
			$this->task_info['boot'] = $info['boot'] ?? $this->task_info['boot'];
			if (in_array($this->task_info['boot'], ['off', 'offing'], true)) {
				$this->shouldStop = true;
			}
		}
		$this->task_info['run_at'] = $now;
		$this->task_info['memory_usage'] = memory_get_usage() - APP_MEMORY_START;
		$this->setCache($key, $this->task_info);
		$this->lastSyncAt = $now;
		return !$this->shouldStop;
	}

	/**
	 * 兼容旧版子任务中的 taskMonitor 调用
	 */
	protected function taskMonitor(): bool
	{
		return true;
	}

	/**
	 * 发送唤醒信号给 MainTask
	 */
	protected function signal(): void
	{
		$this->redis()->lPush(self::SIGNAL_KEY, '1');
	}

	/**
	 * 计算多条 cron 配置中最早的下次运行时间戳
	 */
	public function getNextTime(array $crons): int
	{
		if (empty($crons)) {
			return 0;
		}
		$result = 0;
		foreach ($crons as $val) {
			$val = trim((string)$val);
			if ($val === '' || $val === '* * * * *') {
				continue;
			}
			$v = $this->getNextTimeByCron($val);
			if ($v > 0 && ($result === 0 || $v < $result)) {
				$result = $v;
			}
		}
		return $result;
	}

	/**
	 * 计算单条 crontab 表达式的下一次运行时间戳
	 * 格式: 分(0-59) 时(0-23) 日(1-31) 月(1-12) 周(0-6)
	 */
	protected function getNextTimeByCron(string $cronStr): int
	{
		$cronStr = trim(preg_replace('/\s+/', ' ', $cronStr));
		if ($cronStr === '' || $cronStr === '* * * * *') {
			return 0;
		}
		$arr = explode(' ', $cronStr);
		if (count($arr) < 5) {
			return 0;
		}

		$minuteRange = $this->cronUnitParse($arr[0], 0, 59);
		$hourRange   = $this->cronUnitParse($arr[1], 0, 23);
		$monthRange  = $this->cronUnitParse($arr[3], 1, 12);
		$weekRange   = $this->cronUnitParse($arr[4], 0, 6, true);

		if (empty($minuteRange) || empty($hourRange) || empty($monthRange)) {
			return 0;
		}

		// 从下一整分钟开始匹配
		$ts = time() + 60;
		$year   = (int)date('Y', $ts);
		$month  = (int)date('m', $ts);
		$day    = (int)date('d', $ts);
		$hour   = (int)date('H', $ts);
		$minute = (int)date('i', $ts);

		// 最多向后检索 366 天
		for ($i = 0; $i < 366; $i++) {
			// 1. 校验月份
			$nextMonth = $this->cronNextVal($monthRange, $month);
			if ($nextMonth < 0) {
				$year++;
				$month  = $monthRange[0];
				$day    = 1;
				$hour   = 0;
				$minute = 0;
				continue;
			}
			if ($nextMonth > $month) {
				$month  = $nextMonth;
				$day    = 1;
				$hour   = 0;
				$minute = 0;
			}

			// 2. 校验日期或星期
			$firstOfMonthTs = mktime(0, 0, 0, $month, 1, $year);
			$daysInMonth = (int)date('t', $firstOfMonthTs);
			if ($day > $daysInMonth) {
				$month++;
				if ($month > 12) {
					$year++;
					$month = 1;
				}
				$day    = 1;
				$hour   = 0;
				$minute = 0;
				continue;
			}

			$dayMatched = false;
			if ($arr[4] === '*' || $arr[2] !== '*') {
				$dayRange = $this->cronUnitParse($arr[2], 1, $daysInMonth);
				$dayMatched = in_array($day, $dayRange, true);
			} else {
				$weekDay = (int)date('w', mktime(0, 0, 0, $month, $day, $year));
				$dayMatched = in_array($weekDay, $weekRange, true);
			}

			if (!$dayMatched) {
				$nextDayTs = mktime(0, 0, 0, $month, $day + 1, $year);
				$year   = (int)date('Y', $nextDayTs);
				$month  = (int)date('m', $nextDayTs);
				$day    = (int)date('d', $nextDayTs);
				$hour   = 0;
				$minute = 0;
				continue;
			}

			// 3. 校验小时
			$nextHour = $this->cronNextVal($hourRange, $hour);
			if ($nextHour < 0) {
				$nextDayTs = mktime(0, 0, 0, $month, $day + 1, $year);
				$year   = (int)date('Y', $nextDayTs);
				$month  = (int)date('m', $nextDayTs);
				$day    = (int)date('d', $nextDayTs);
				$hour   = 0;
				$minute = 0;
				continue;
			}
			if ($nextHour > $hour) {
				$hour   = $nextHour;
				$minute = 0;
			}

			// 4. 校验分钟
			$nextMinute = $this->cronNextVal($minuteRange, $minute);
			if ($nextMinute < 0) {
				$hour++;
				$minute = 0;
				if ($hour > 23) {
					$nextDayTs = mktime(0, 0, 0, $month, $day + 1, $year);
					$year  = (int)date('Y', $nextDayTs);
					$month = (int)date('m', $nextDayTs);
					$day   = (int)date('d', $nextDayTs);
					$hour  = 0;
				}
				continue;
			}

			return mktime($hour, $nextMinute, 0, $month, $day, $year);
		}

		return 0;
	}

	/**
	 * 解析 cron 单个字段, 生成有序结果集
	 */
	protected function cronUnitParse(string $unit, int $min, int $max, bool $isWeek = false): array
	{
		$step = 1;
		$str = $unit;
		if (($pos = strpos($str, '/')) !== false) {
			$step = max(1, (int)substr($str, $pos + 1));
			$str = substr($str, 0, $pos);
		}
		if ($str === '*') {
			$result = [];
			for ($v = $min; $v <= $max; $v += $step) {
				$result[] = $v;
			}
			return $result;
		}
		$values = [];
		foreach (explode(',', $str) as $part) {
			if (($dashPos = strpos($part, '-')) !== false) {
				$lo = (int)substr($part, 0, $dashPos);
				$hi = (int)substr($part, $dashPos + 1);
				for ($v = $lo; $v <= $hi; $v++) {
					if ($isWeek && $v === 7) {
						$v = 0;
					}
					if ($v >= $min && $v <= $max) {
						$values[] = $v;
					}
				}
			} else {
				$v = (int)$part;
				if ($isWeek && $v === 7) {
					$v = 0;
				}
				if ($v >= $min && $v <= $max) {
					$values[] = $v;
				}
			}
		}
		$values = array_values(array_unique($values));
		sort($values);
		if ($step > 1) {
			$result = [];
			for ($i = 0, $cnt = count($values); $i < $cnt; $i += $step) {
				$result[] = $values[$i];
			}
			return $result;
		}
		return $values;
	}

	/**
	 * 在有序数组中找 >= $val 的最小值 (二分查找)
	 */
	protected function cronNextVal(array $range, int $val): int
	{
		$cnt = count($range);
		if ($cnt === 0 || $val > $range[$cnt - 1]) {
			return -1;
		}
		if ($val <= $range[0]) {
			return $range[0];
		}
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

	protected function echo(string $info)
	{
		$this->task_info['remark'] = $info;
	}

	/**
	 * 阻塞等待信号唤醒或超时
	 * @param int $timeout 最大等待秒数
	 */
	protected function waitForSignal(int $timeout)
	{
		if ($timeout <= 0 || $this->shouldStop) {
			return;
		}
		$end = time() + $timeout;
		// 分段 blPop (最大55秒), 兼顾信号感知与超时连接保活
		while (!$this->shouldStop && ($remain = $end - time()) > 0) {
			$wait = min($remain, 55);
			$result = $this->redis()->blPop(self::SIGNAL_KEY, $wait);
			if ($result) {
				// 合并排空多余信号, 避免瞬间多次信号触发空转
				$this->redis()->del(self::SIGNAL_KEY);
				return;
			}
		}
	}

	protected function getCache(string $key, string $type = 'list')
	{
		return $this->redis()->hGet($this->getKey($type), static::formatKey($key));
	}

	protected function setCache(string $key, $value, string $type = 'list')
	{
		return $this->redis()->hSet($this->getKey($type), static::formatKey($key), $value);
	}

	protected function getKey(string $type = 'list'): string
	{
		return self::TASKPREFIX . $type;
	}

	protected function redis(int $db = 2)
	{
		return redis($db);
	}
}