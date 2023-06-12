<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Index extends HomeBase
{
	public function index()
	{
		make('app\task\MainTask')->run();
		html()->addCss();
		html()->addJs();
		$this->view(true);
	}
}