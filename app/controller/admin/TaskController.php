<?php

namespace app\controller\admin;
use app\controller\Controller;

class TaskController extends Controller
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
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['taskList'])) {
				$this->$opn();
			}
			$this->error('Unknown request');
		}
		html()->addCss();
		html()->addJs();
		$this->view();
	}

	protected function taskList()
	{
		$prev = 'frame-task:';
		$taskList = redis(2)->smembers($prev.'all');
		$list = [];
		if (!empty($taskList)) {
			foreach($taskList as $value) {
				$data = redis(2)->hGetAll($prev.$value);
				$data['name'] = $value;
				$list[] = $data;
			}
		}
		$this->success(['time'=>now(), 'list'=>$list]);
	}
}