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
		$taskList = getDirFile(ROOT_PATH.'app'.DS.'task'.DS.'main');
        foreach ($taskList as $key => $value) {
            //重新缓存配置
            $taskList[$key] = nameFormat(str_replace([ROOT_PATH, '.php'], '', $value));
        }
		array_unshift($taskList, 'app-task-MainTask');
		$taskList = array_flip($taskList);
		foreach($taskList as $key=>$value) {
			$value = $this->cache()->hGetAll($this->getKey($key));
			if (empty($value)) return [];
			$taskList[$key] = $value;
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
		switch ($type) {
			case 'init':
				$key = 'app-task-MainTask';
				if ($this->cache()->exists($this->getKey($key, 'lock'))) {
					$this->error('进程未停止, 请稍后再试');
				}
				make('frame/Task')->start($key);
				break;
			case 'start':
			case 'stop':
				if (empty($key)) {
					$this->error('未指定任务开启');
				}
				if ($key == 'app-task-MainTask') {
					$this->cache()->hSet($this->getKey($key), 'boot', $type=='start'?'oning':'offing');
				} else {
					$value = $this->cache()->hGet($this->getKey('all'), $key);
					if ($value['boot'] == 'on') {
						if ($value['status'] == 'stop') {
							$value['boot'] = 'off';
						} else {
							$value['boot'] = 'offing';
						}
					} else {
						$value['boot'] = 'oning';
						$value['next_run'] = 'alwaysRun';
					}
					$this->cache()->hSet($this->getKey('all'), $key, $value);
					$this->cache()->hSet($this->getKey($key), 'boot', $value['boot']);
				}
				if ($type == 'start' && $key == 'app-task-MainTask') {
					make('frame/Task')->start($key);
				}
				break;
			case 'start-all':
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
		return ($type=='lock'?self::LOCKERPREFIX:self::TASKPREFIX).$key;
	}
}