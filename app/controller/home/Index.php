<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Index extends HomeBase
{
	public function index()
	{
		make('app/task/main/CompressStatic')->run();
		html()->addCss();
		html()->addJs();
		$this->view(true);
	}
}