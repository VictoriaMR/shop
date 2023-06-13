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
		$list = make('frame/Task')->getTaskList(true);
		//压入主任务
		
		dd($list);
		array_unshift($taskList, 'app-task-MainTask');
		$taskList = array_flip($taskList);
		foreach($taskList as $key=>$value) {
			$value = $this->cache()->hGetAll($this->getKey($key));
			if (empty($value)) {
				$class = make(strtr($key, '-', DS), null, false);
				$taskList[$key] = [
					'boot' => 'off',
					'status' => 'stop',
					'name' => $class->config['name'],
					'class_name' => $key,
				];
			} else {
				$taskList[$key] = $value;
			}
		}
		return $taskList;
	}

	protected function modifyTask()
	{
		$type = ipost('type');
		$key = trim(ipost('key', ''));
		$tasker = make('frame/Task');
		switch ($type) {
			case 'start':
			case 'stop':
				if (empty($key)) {
					$this->error('未指定任务');
				}
				$status = $type=='start'?'on':'off';
				$tasker->setInfo($key, 'boot', $status);
				//更新总任务数据
				$allInfo = $tasker->getInfo('all', $key);
				$allInfo['boot'] = $status;
				$allInfo['status'] = $status.'ing';
				$tasker->setInfo('all', $key, $allInfo);
				$tasker->setInfoArray($key, $allInfo);
				if ($status == 'on') {
					$tasker->start($key);
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
		return cache(0);
	}

	protected function getKey($key, $type='task')
	{
		return ($type=='lock'?self::LOCKERPREFIX:self::TASKPREFIX).$key;
	}
}