<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Index extends HomeBase
{
	public function index()
	{
		make('app/task/MainTask', ['cas'=>'app-task-MainTask', 'lock'=>'112233'])->run();
		html()->addCss();
		html()->addJs();
		$this->assign('_title', distT('title'));
		$this->view(true);
	}
}