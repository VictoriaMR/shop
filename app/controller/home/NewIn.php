<?php

namespace app\controller\home;
use app\controller\HomeBase;

class NewIn extends HomeBase
{
	public function index()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();

		$this->view();
	}
}
