<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Task extends AdminBase
{
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
		sys()->currency()->updateRate();
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['modifyTask'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		// frame('Task')->noticeTask('app/task/main/Queue');
		frame('Html')->addCss();
		frame('Html')->addJs();
		$list = frame('Task')->getTaskList(true);
		foreach ($list as $key=>$value) {
			if (!isset($value['boot'])) {
				$list[$key]['boot'] = 'off';
			}
			if (!isset($value['status'])) {
				$list[$key]['status'] = 'stop';
			}
		}
		$this->view([
			'taskList' => $list,
		]);
	}

	protected function modifyTask()
	{
		$type = ipost('type');
		$key = trim(ipost('key', ''));
		$tasker = frame('Task');
		switch ($type) {
			case 'start':
			case 'stop':
				if (empty($key)) {
					$this->error('未指定任务');
				}
				$tasker->boot($key, $type=='start'?'on':'off');
				break;
			case 'start-all':
			case 'stop-all':
				$list = frame('Task')->getTaskList(true);
				foreach ($list as $key=>$value) {
					$tasker->boot($key, $type=='start-all'?'on':'off');
				}
				break;
			case 'reset':
				if ($key != 'app/task/MainTask') {
					$this->error('非主进程任务不能重置');
				}
				frame('Task')->start($key);
				break;
		}
		$this->success('操作成功');
	}
}