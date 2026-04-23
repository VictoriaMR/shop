<?php

namespace app\task;
use app\task\TaskDriver;

class MainTask extends TaskDriver
{
	public $config = [
		'name' => '系统核心队列任务',
		'cron' => ['* * * * *'],
	];
	protected $allTask = [];
	// 任务超时阈值(秒)
	protected $timeout = 600;

	/**
	 * 扫描并启动到期任务
	 * @return int 下次唤醒等待秒数, 0=立即再次执行
	 */
	public function run()
	{
		$now = time();
		$total = count($this->allTask);
		$off = 0;
		$memoryUsage = 0;
		$minWait = 3600; // 无到期任务时最大等待1小时

		foreach ($this->allTask as $key => $value) {
			$memoryUsage += ($value['memory_usage'] ?? 0);
			if ($key === 'app-task-MainTask') continue;

			$boot = $value['boot'] ?? 'off';

			// 已关闭 → 快速跳过
			if ($boot === 'off') {
				$off++;
				continue;
			}
			// 关闭中且不在运行 → 标记为已关闭
			if ($boot === 'offing' && ($value['status'] ?? '') !== 'running') {
				$this->allTask[$key]['boot'] = 'off';
				$this->allTask[$key]['status'] = 'stop';
				$this->tasker->setInfo($key, $this->allTask[$key]);
				$off++;
				continue;
			}

			$status = $value['status'] ?? 'stop';
			$nextRun = $value['next_run'] ?? 0;

			// 持续运行的任务(cron=* * * * *)：next_run=0, 退出即重启
			if ($nextRun === 0) {
				if ($status === 'stop') {
					$this->startTask($key, $value);
				}
				// 持续任务随时可能退出, 保持较短等待
				$minWait = min($minWait, 5);
				continue;
			}

			// 需要启动: 到达执行时间 或 运行超时
			if (($status === 'stop' && $nextRun <= $now)
				|| ($status === 'running' && ($now - ($value['run_at'] ?? 0) > $this->timeout))
			) {
				$this->startTask($key, $value);
			} elseif ($status === 'stop' && $nextRun > $now) {
				// 未到期, 计算等待时间
				$minWait = min($minWait, $nextRun - $now);
			}
		}

		$active = $total - $off;
		$msg = [
			"任务总数: {$total}",
			"开启任务数: {$active}",
			"关闭任务数: {$off}",
			'总运行内存: ' . get1024Peck($memoryUsage),
			"下次唤醒: {$minWait}s",
		];
		$this->echo(implode(PHP_EOL, $msg));
		return $minWait;
	}

	protected function startTask($key, $value)
	{
		$value['status'] = 'starting';
		$this->tasker->setInfo($key, $value);
		$this->tasker->start($key);
	}
}