<?php

namespace app\controller\admin;
use app\controller\Controller;

class TaskController extends Controller
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
			if (in_array($opn, ['taskList', 'modifyTask'])) {
				$this->$opn();
			}
			$this->error('Unknown request');
		}
		html()->addCss();
		html()->addJs();
		$this->assign('enabled', config('task.enabled'));
		$this->view();
	}

	protected function taskList()
	{
		$taskList = redis(2)->smembers(self::TASKPREFIX.'all');
		$list = [];
		if (!empty($taskList)) {
			$mainIndex = array_search('app-task-MainTask', $taskList);
			$data = redis(2)->hGetAll(self::TASKPREFIX.$taskList[$mainIndex]);
			$data['name'] = $taskList[$mainIndex];
			$list[] = $data;
			unset($taskList[$mainIndex]);
			foreach($taskList as $value) {
				$data = redis(2)->hGetAll(self::TASKPREFIX.$value);
				$data['name'] = $value;
				$list[] = $data;
			}
		}
		$this->success(['time'=>now(), 'list'=>$list]);
	}

	protected function modifyTask()
	{
		$type = ipost('type');
		$key = ipost('key');
		$enabled = config('task.enabled');
		if (in_array($type, ['startup_all', 'shutdown_all'])) {
			$taskList = redis(2)->smembers(self::TASKPREFIX.'all');
			if (!empty($taskList)) {
				foreach($taskList as $value) {
					redis(2)->hSet(self::TASKPREFIX.$value, 'boot', $type == 'startup_all' && $enabled ? 'on' : 'off');
				}
			}
			if ($type == 'startup_all' && $enabled) {
				make('frame/Task')->start();
			}
		} else {
			if (empty($key)) {
				$this->error('进程名称不能为空');
			}
			if ($type == 'shutdown') {
				redis(2)->del(self::LOCKERPREFIX.$key);
			}
			redis(2)->hSet(self::TASKPREFIX.$key, 'boot', $type == 'startup' && $enabled ? 'on' : 'off');
			if (!$enabled) {
				$this->error('当前任务配置未开启');
			}
			if ($key == 'app-task-MainTask' && $type == 'startup') {
				make('frame/Task')->start();
			}
		}
		$this->success('操作成功');
	}
}