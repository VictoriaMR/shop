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
			if (in_array($opn, ['taskList', 'modifyTask'])) {
				$this->$opn();
			}
			$this->error('Unknown request');
		}
		html()->addCss();
		html()->addJs();
		$this->assign('enabled', config('task', 'enabled'));
		$this->view();
	}

	protected function taskList()
	{
		$path = ROOT_PATH.'app'.DS.'task'.DS.'main';
		$taskList = getDirFile($path);
		if (empty($taskList)) {
			$this->error('任务列表为空');
		}
		foreach($taskList as $key=>$value) {
			$name = strtr(str_replace([ROOT_PATH, '.php'], '', $value), DS, '/');
			$cacheKey = strtr(self::TASKPREFIX.$name, '/', '-');
			$data = cache(2)->hGetAll($cacheKey);
			if ($data == false) {
				$object = make($name);
				$data = [
					'name' => $name,
					'boot' => 'off',
					'info' => $object->config['info'],
				];
				cache(2)->hMset($cacheKey, $data);
			}
			$taskList[$key] = $data;
		}
		$this->success(['time'=>now(), 'list'=>$taskList]);
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
		if (empty($key)) {
			$this->error('进程名称不能为空');
		}
		if ($type == 'startup' && cache(2)->exists(self::LOCKERPREFIX.$key)) {
			$this->error('进程未停止, 请稍后再试');
		}
		cache(2)->hSet(self::TASKPREFIX.$key, 'boot', $type == 'startup' ? 'oning' : 'offing');
		if ($type == 'startup') {
			make('frame/Task')->start($key);
		}
		$this->success('操作成功');
	}
}