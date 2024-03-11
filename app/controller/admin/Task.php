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
		$list = make('frame/Task')->getTaskList(true);
		foreach ($list as $key=>$value) {
			if (!isset($value['boot'])) {
				$list[$key]['boot'] = 'off';
			}
			if (!isset($value['status'])) {
				$list[$key]['status'] = 'stop';
			}
		}
		$this->assign('taskList', $list);
		$this->view();
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

	protected function getKey($key, $type='task')
	{
		return ($type=='lock'?self::LOCKERPREFIX:self::TASKPREFIX).$key;
	}
}