<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Task extends AdminBase
{
	const TASKPREFIX ='frame-task:';
	const LOCKERPREFIX = 'frame-lock:';

	public function __construct()
	{
		$this->_arr = [
			'index' => '定时任务管理',
		];
		$this->_default = '任务管理';
		$this->_init();
	}

	public function index()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['modifyTask'])) {
				$this->$opn();
			}
			$this->error('Unknown request');
		}
		html()->addCss();
		html()->addJs();
		$this->assign('taskList', $this->taskList());
		$this->assign('enabled', config('task', 'enabled'));
		$this->view();
	}

	protected function taskList()
	{
		$taskList = $this->cache()->hGetAll($this->getKey('all'));
		if (empty($taskList)) {
			return [];
		}
		$taskList = array_keys($taskList);
		array_unshift($taskList, 'app-task-MainTask');
		$taskList = array_flip($taskList);
		foreach($taskList as $key=>$value) {
			$taskList[$key] = $this->cache()->hGetAll($this->getKey($key));
		}
		return $taskList;
	}

	protected function modifyTask()
	{
		$type = ipost('type');
		$key = trim(ipost('key', ''));
		$enabled = config('task', 'enabled');
		if (!$enabled) {
			$this->error('系统任务开关未开启');
		}
		$key = strtr($key, '/', '-');
		switch ($type)
		{
			case 'init':
				$key = 'app-task-MainTask';
				if ($this->cache()->exists($this->getKey($key), 'lock')) {
					$this->error('进程未停止, 请稍后再试');
				}
				make('frame/Task')->start($key);
				break;
			case 'start':
				if (empty($key)) {

				}
				break;
			case 'stop':
				break;
			case 'start-all':
				break;
			case 'stop-all':
				break;
		}
		$this->success('操作成功');
	}

	protected function cache()
	{
		return cache(2);
	}

	protected function getKey($key, $type='task')
	{
		return ($type=='task'?self::TASKPREFIX:self::LOCKERPREFIX).$key;
	}
}