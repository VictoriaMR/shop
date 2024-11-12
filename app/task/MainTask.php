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

	public function run()
	{
		$data = [
			'memory_usage' => 0,
			'total' => count($this->allTask),
			'off' => 0
		];
		foreach ($this->allTask as $key=>$value) {
			$data['memory_usage'] += ($value['memory_usage'] ?? 0);
			if ($key == 'app-task-MainTask') continue;
			//循环检查进程状态
			if (in_array($value['boot'], ['off', 'offing'])) {
				if ($value['boot'] == 'offing' && $value['status'] != 'running') {
					$this->allTask[$key]['boot'] = 'off';
					$this->allTask[$key]['status'] = 'stop';
					$this->tasker->setInfo($key, $this->allTask[$key]);
				}
				$data['off'] +=1;
				continue;
			}
			// 开启进程
			if (($value['status'] == 'stop' && $value['next_run'] > 0 && $value['next_run'] <= time())
				|| ($value['status'] == 'running' && (time() - $value['run_at'] > 600))
			) {
				$this->allTask[$key]['status'] = 'starting';
				$this->startTask($key, $this->allTask[$key]);
			}
		}
		$msg = [];
		$msg[] = '任务总数: '.$data['total'];
		$msg[] = '开启任务数: '.($data['total'] - $data['off']);
		$msg[] = '关闭任务数: '.$data['off'];
		$msg[] = '总运行内存: '.get1024Peck($data['memory_usage']);
		$this->echo(implode(PHP_EOL, $msg));
		return true;
	}

	protected function startTask($key, $value)
	{
		$value['status'] = 'starting';
		$this->tasker->setInfo($key, $value);
		$this->tasker->start($key);
	}
}