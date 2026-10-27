<?php

namespace app\task;

class MainTask extends TaskDriver
{
	public static $config = [
		'name' => '系统核心队列任务',
		'type' => self::TYPE_MAIN,
		'cron' => ['* * * * *'],
	];

	/** @var array<string, array> 定时子任务的 cron 配置缓存 */
	protected $taskCrons = [];

	/** @var array<string, int> 定时子任务下次触发时间戳标记缓存 */
	protected $scheduleMap = [];

	/** @var int 下轮阻塞等待秒数(首次为0立即检查) */
	protected $wait = 0;

	protected function beforeStart()
	{
		$now = time();
		$redis = $this->redis();

		// 排空历史残留信号与延时队列, 重新校准所有子任务标记
		$redis->del(self::SIGNAL_KEY);
		$redis->del(self::DELAY_KEY);

		$allCache = $redis->hGetAll(self::LIST_KEY) ?: [];
		// 清理历史遗留的非标准键名 (如 app-task-* 或含反斜杠的键名)
		foreach ($allCache as $rawKey => $val) {
			$normKey = self::formatKey($rawKey);
			if ($normKey !== $rawKey) {
				if (!isset($allCache[$normKey]) && is_array($val)) {
					$allCache[$normKey] = $val;
					$this->setCache($normKey, $val);
				}
				$redis->hDel(self::LIST_KEY, $rawKey);
			}
		}

		$taskFiles = getDirFile(ROOT_PATH . 'app/task/main');
		foreach ($taskFiles as $filePath) {
			if (substr($filePath, -4) !== '.php') {
				continue;
			}
			$classPath = self::formatKey(str_replace([ROOT_PATH, '.php'], '', $filePath));
			\App::autoload($classPath);
			$className = strtr($classPath, '/', '\\');
			if (!class_exists($className, false)) {
				continue;
			}

			$config = $className::$config ?? [];
			$type = self::resolveType($config, $classPath);
			$info = is_array($allCache[$classPath] ?? null) ? $allCache[$classPath] : [];

			$info['name'] = $config['name'] ?? ($info['name'] ?? $classPath);
			$info['type'] = $type;
			$info['cron'] = $config['cron'] ?? [];

			// 规范化开关状态
			$boot = $info['boot'] ?? 'on';
			if ($boot === 'oning') {
				$boot = 'on';
			} elseif ($boot === 'offing') {
				$boot = 'off';
			}
			$info['boot'] = $boot;

			// 检查进程是否存活(Linux实时检测PID, Windows检测超时)
			$status = $info['status'] ?? 'stop';
			if ($status === 'running' && !self::isProcessRunning($info, $this->timeout)) {
				$status = 'stop';
				$info['process_pid'] = 0;
			}
			$info['status'] = $status;

			if ($type === self::TYPE_CRON) {
				$this->taskCrons[$classPath] = $info['cron'];
				if ($boot === 'on') {
					$nextRun = (int)($info['next_run'] ?? 0);
					if ($nextRun <= $now) {
						$nextRun = $this->getNextTime($info['cron']);
					}
					$info['next_run'] = $nextRun;
					if ($nextRun > 0 && $status !== 'running') {
						$this->setMarker($classPath, $nextRun);
					}
				} else {
					$info['next_run'] = 0;
				}
			} else {
				// 其他任务: 按需触发, 不设固定周期标记
				$info['next_run'] = 0;
			}

			unset($info['run_next']);
			$this->setCache($classPath, $info);
		}
	}

	/**
	 * 设置子任务调度时间标记
	 */
	protected function setMarker(string $classPath, int $nextRun): void
	{
		if ($nextRun > 0) {
			$this->scheduleMap[$classPath] = $nextRun;
			$this->redis()->zAdd(self::DELAY_KEY, $nextRun, $classPath);
		} else {
			$this->removeMarker($classPath);
		}
	}

	/**
	 * 移除子任务调度时间标记
	 */
	protected function removeMarker(string $classPath): void
	{
		unset($this->scheduleMap[$classPath]);
		$this->redis()->zRem(self::DELAY_KEY, $classPath);
	}

	/**
	 * 常驻主循环: 阻塞等待信号或到点 → 同步状态与异常恢复 → 触发到期子任务 → 计算下轮等待时长
	 */
	public function run()
	{
		// 阻塞等待信号唤醒或最近一个定时任务到点 (首次 wait=0 不休眠)
		$this->waitForSignal($this->wait);
		if ($this->shouldStop) {
			return false;
		}

		$now = time();
		$redis = $this->redis();
		$allInfo = $redis->hGetAll(self::LIST_KEY) ?: [];

		// 1. 检查主进程自身是否收到停止指令或已被新进程接管
		$mainInfo = $allInfo[self::MAIN_TASK] ?? [];
		if (in_array($mainInfo['boot'] ?? 'on', ['off', 'offing'], true)) {
			$this->task_info['boot'] = 'off';
			return false;
		}
		$cachedPid = (int)($mainInfo['process_pid'] ?? 0);
		if ($cachedPid > 0 && $cachedPid !== (int)$this->task_info['process_pid']) {
			$this->shouldStop = true;
			return false;
		}

		// 2. 巡检子任务状态: 处理开关过渡态、超时/异常退出任务恢复及定时标记校准
		foreach ($allInfo as $classPath => $info) {
			if ($classPath === self::MAIN_TASK || !is_array($info)) {
				continue;
			}
			$changed = false;
			$type = $info['type'] ?? self::resolveType($info, $classPath);
			$boot = $info['boot'] ?? 'off';
			$status = $info['status'] ?? 'stop';
			$isDead = ($status === 'running') && !self::isProcessRunning($info, $this->timeout);

			// 超时或异常退出的任务自动复位
			if ($isDead) {
				$status = 'stop';
				$info['status'] = 'stop';
				$info['process_pid'] = 0;
				$info['remark'] = '进程已结束或超时自动重置';
				$changed = true;
			}

			// 处理启动中过渡态
			if ($boot === 'oning') {
				$boot = 'on';
				$info['boot'] = 'on';
				$changed = true;
			}

			// 处理停止中过渡态
			if ($boot === 'offing' || $boot === 'off') {
				$this->removeMarker($classPath);
				if ($status !== 'running') {
					if ($boot !== 'off' || ($info['next_run'] ?? 0) != 0) {
						$info['boot'] = 'off';
						$info['next_run'] = 0;
						$changed = true;
					}
				}
			} elseif ($boot === 'on' && $type === self::TYPE_CRON && $status !== 'running') {
				// 定时任务开启且未在运行中: 确保在 MainTask 有下次触发标记
				$nextRun = (int)($info['next_run'] ?? 0);
				if ($nextRun <= 0) {
					$crons = $this->taskCrons[$classPath] ?? ($info['cron'] ?? []);
					$nextRun = $this->getNextTime($crons);
					$info['next_run'] = $nextRun;
					$changed = true;
				}
				if ($nextRun > 0 && ($this->scheduleMap[$classPath] ?? 0) !== $nextRun) {
					$this->setMarker($classPath, $nextRun);
				}
			}

			if ($changed) {
				$allInfo[$classPath] = $info;
				$this->setCache($classPath, $info);
			}
		}

		// 3. 拉取所有到点的任务标记并触发子进程
		$dueTasks = $redis->zRangeByScore(self::DELAY_KEY, 0, $now);
		if (!empty($dueTasks)) {
			foreach ($dueTasks as $classPath) {
				$classPath = self::formatKey($classPath);
				// 触发前先移除当前到点标记, 由子任务运行完成后更新下一次标记
				$this->removeMarker($classPath);

				$info = $allInfo[$classPath] ?? $this->getCache($classPath);
				if (empty($info) || !is_array($info)) {
					continue;
				}
				if (($info['boot'] ?? 'off') !== 'on') {
					continue;
				}
				if (self::isProcessRunning($info, $this->timeout)) {
					continue;
				}
				frame('Task')->start($classPath);
			}
		}

		// 4. 查询最近一条未到期标记, 精确计算下次阻塞等待秒数 (上限180秒以便定期刷新主进程心跳与巡检超时)
		$next = $redis->zRange(self::DELAY_KEY, 0, 0, true);
		if (empty($next)) {
			$this->wait = 180;
		} else {
			$nextTs = (int)current($next);
			$this->scheduleMap[key($next)] = $nextTs;
			$this->wait = max(1, min(180, $nextTs - time()));
		}

		return true;
	}
}