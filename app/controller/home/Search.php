<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Search extends HomeBase
{
	public function index()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$this->view();
	}
}
