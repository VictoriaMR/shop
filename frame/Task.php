<?php

namespace frame;
use app\task\TaskDriver;

class Task
{
	const TASKPREFIX = TaskDriver::TASKPREFIX;

	/** @var array<string, string> classKey 缓存 */
	private $classKeyCache = [];

	/**
	 * 启动后台 CLI 任务进程
	 */
	public function start(string $taskClass): bool
	{
		$classPath = $this->getClassKey($taskClass);
		return $this->localRunPhp($classPath . ' start');
	}

	/**
	 * 异步拉起本地 PHP CLI 进程 (兼容 Windows 本地开发与 Linux 生产环境)
	 */
	public function localRunPhp(string $param): bool
	{
		$phpBin = config('task', 'phpbin', 'php');
		$cmd = $phpBin . ' -f ' . ROOT_PATH . 'command ' . $param;
		if (isWin()) {
			pclose(popen('start /B ' . $cmd . ' 1>NUL 2>NUL', 'r'));
		} else {
			exec('nohup ' . $cmd . ' > /dev/null 2>&1 &');
		}
		frame('Debug')->runlog($cmd, 'task');
		return true;
	}

	/**
	 * 统一规范化任务键名 (app/task/...)
	 */
	public function getClassKey(string $classname): string
	{
		if (!isset($this->classKeyCache[$classname])) {
			$this->classKeyCache[$classname] = TaskDriver::formatKey($classname);
		}
		return $this->classKeyCache[$classname];
	}

	/**
	 * 获取任务列表 (按 主任务 -> 定时任务 -> 其他任务 排序)
	 */
	public function getTaskList(bool $main = false): array
	{
		$keys = [];
		if ($main) {
			$keys[] = TaskDriver::MAIN_TASK;
		}
		$subDir = ROOT_PATH . 'app/task/main';
		if (is_dir($subDir)) {
			$files = scandir($subDir);
			foreach ($files as $file) {
				if ($file === '.' || $file === '..' || substr($file, -4) !== '.php') {
					continue;
				}
				$keys[] = 'app/task/main/' . substr($file, 0, -4);
			}
		}

		$timeout = (int)config('task', 'timeout', 600);
		$listInfo = $this->getInfo() ?: [];

		// 兼容迁移历史遗留的横杠/反斜杠键名
		foreach ($listInfo as $rawKey => $val) {
			$normKey = $this->getClassKey($rawKey);
			if ($normKey !== $rawKey) {
				if (!isset($listInfo[$normKey]) && is_array($val)) {
					$listInfo[$normKey] = $val;
					$this->setInfo($normKey, $val);
				}
				redis(2)->hDel(TaskDriver::LIST_KEY, $rawKey);
				unset($listInfo[$rawKey]);
			}
		}

		$mainList = [];
		$cronList = [];
		$otherList = [];

		foreach ($keys as $key) {
			$config = $this->loadTaskConfig($key);
			$type = TaskDriver::resolveType($config, $key);
			$cached = is_array($listInfo[$key] ?? null) ? $listInfo[$key] : [];

			$info = array_merge([
				'name'         => $config['name'] ?? $key,
				'type'         => $type,
				'cron'         => $config['cron'] ?? [],
				'boot'         => 'off',
				'status'       => 'stop',
				'start_count'  => 0,
				'run_count'    => 0,
				'next_run'     => 0,
				'memory_usage' => 0,
			], $cached, [
				'name' => $config['name'] ?? ($cached['name'] ?? $key),
				'type' => $type,
				'cron' => $config['cron'] ?? ($cached['cron'] ?? []),
			]);

			// 检测进程真实存活状态 (Linux 实时检测 PID, Windows 检测超时)
			if (($info['status'] ?? '') === 'running' && !TaskDriver::isProcessRunning($info, $timeout)) {
				$info['status'] = 'stop';
				$info['process_pid'] = 0;
				if (($info['boot'] ?? '') === 'offing') {
					$info['boot'] = 'off';
				}
				$this->setInfo($key, $info);
			}

			if ($type === TaskDriver::TYPE_MAIN) {
				$mainList[$key] = $info;
			} elseif ($type === TaskDriver::TYPE_CRON) {
				$cronList[$key] = $info;
			} else {
				$otherList[$key] = $info;
			}
		}

		return $mainList + $cronList + $otherList;
	}

	/**
	 * 仅加载类静态配置, 避免不必要实例化开销
	 */
	protected function loadTaskConfig(string $key): array
	{
		$classPath = $this->getClassKey($key);
		$className = strtr($classPath, '/', '\\');
		if (!class_exists($className, false)) {
			\App::autoload($classPath);
		}
		return class_exists($className, false) ? ($className::$config ?? []) : [];
	}

	protected function getKey(string $type = 'list'): string
	{
		return self::TASKPREFIX . $type;
	}

	/**
	 * 开启或关闭任务
	 */
	public function boot(string $key, string $value): bool
	{
		$classKey = $this->getClassKey($key);
		$config = $this->loadTaskConfig($classKey);
		if (empty($config)) {
			return false;
		}
		$type = TaskDriver::resolveType($config, $classKey);
		$info = $this->getInfo($classKey);
		if (empty($info) || !is_array($info)) {
			$info = array_merge($config, [
				'type'        => $type,
				'boot'        => 'off',
				'status'      => 'stop',
				'start_count' => 0,
				'run_count'   => 0,
				'next_run'    => 0,
			]);
		} else {
			$info['name'] = $config['name'] ?? ($info['name'] ?? $classKey);
			$info['type'] = $type;
			$info['cron'] = $config['cron'] ?? ($info['cron'] ?? []);
		}

		$timeout = (int)config('task', 'timeout', 600);
		$isRunning = TaskDriver::isProcessRunning($info, $timeout);

		if ($value === 'on') {
			$info['boot'] = 'on';
			if (!$isRunning) {
				$info['status'] = 'stop';
				$info['process_pid'] = 0;
			}
			if ($classKey === TaskDriver::MAIN_TASK) {
				$this->setInfo($classKey, $info);
				if (!$isRunning) {
					$this->start($classKey);
				}
			} else {
				if ($type === TaskDriver::TYPE_CRON) {
					$nextRun = \App::make(TaskDriver::MAIN_TASK)->getNextTime($info['cron'] ?? []);
					$info['next_run'] = $nextRun;
					if ($nextRun > 0 && !$isRunning) {
						redis(2)->zAdd(TaskDriver::DELAY_KEY, $nextRun, $classKey);
					}
				} else {
					$info['next_run'] = 0;
				}
				$this->setInfo($classKey, $info);
				$this->signal();
			}
		} else {
			$info['boot'] = $isRunning ? 'offing' : 'off';
			if (!$isRunning) {
				$info['status'] = 'stop';
				$info['process_pid'] = 0;
			}
			$info['next_run'] = 0;
			redis(2)->zRem(TaskDriver::DELAY_KEY, $classKey);
			$this->setInfo($classKey, $info);
			$this->signal();
		}

		return true;
	}

	public function setInfo(string $field, array $data)
	{
		return redis(2)->hSet(TaskDriver::LIST_KEY, $this->getClassKey($field), $data);
	}

	public function getInfo(string $field = '')
	{
		if ($field !== '') {
			return redis(2)->hGet(TaskDriver::LIST_KEY, $this->getClassKey($field));
		}
		return redis(2)->hGetAll(TaskDriver::LIST_KEY);
	}

	/**
	 * 触发子任务执行 (写入到期标记并唤醒 MainTask)
	 */
	public function noticeTask(string $key, bool $force = false): bool
	{
		$classKey = $this->getClassKey($key);
		if ($classKey === TaskDriver::MAIN_TASK) {
			return false;
		}
		$config = $this->loadTaskConfig($classKey);
		if (empty($config)) {
			return false;
		}
		$type = TaskDriver::resolveType($config, $classKey);
		$info = $this->getInfo($classKey);
		if (empty($info) || !is_array($info)) {
			$info = array_merge($config, [
				'type'        => $type,
				'boot'        => 'on',
				'status'      => 'stop',
				'start_count' => 0,
				'run_count'   => 0,
				'next_run'    => 0,
			]);
		}

		if (!$force && ($info['boot'] ?? 'off') !== 'on') {
			return false;
		}

		$now = time();
		$timeout = (int)config('task', 'timeout', 600);
		// 任务正在运行中则不重复触发, 节省进程与Redis开销
		if (TaskDriver::isProcessRunning($info, $timeout)) {
			return true;
		}

		if ($force && ($info['boot'] ?? 'off') !== 'on') {
			$info['boot'] = 'on';
		}

		$info['next_run'] = $now;
		$this->setInfo($classKey, $info);

		// 检查 MainTask 是否在线运行: 在线则通过标记+信号由 MainTask 统一调度, 否则在强制执行时直接拉起
		$mainInfo = $this->getInfo(TaskDriver::MAIN_TASK);
		$mainRunning = is_array($mainInfo) && TaskDriver::isProcessRunning($mainInfo, $timeout);

		if ($mainRunning || !$force) {
			redis(2)->zAdd(TaskDriver::DELAY_KEY, $now, $classKey);
			$this->signal();
		} else {
			$this->start($classKey);
		}

		return true;
	}

	/**
	 * 业务侧触发子任务快捷入口 (如 frame('Task')->taskStart('Queue'))
	 */
	public function taskStart(string $name, bool $force = false): bool
	{
		if ($name === 'EmailTask') {
			$name = 'Email';
		}
		$classKey = (strpos($name, 'app/task/') === 0) ? $name : 'app/task/main/' . $name;
		return $this->noticeTask($classKey, $force);
	}

	/**
	 * 唤醒常驻主进程 MainTask
	 */
	public function signal(): void
	{
		redis(2)->lPush(TaskDriver::SIGNAL_KEY, '1');
	}
}