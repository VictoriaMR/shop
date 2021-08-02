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
		
		$this->view();
	}
}