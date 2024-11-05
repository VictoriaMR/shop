<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Faq extends HomeBase
{
	public function index()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$this->view();
	}
}