<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Index extends HomeBase
{
	public function index()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();

		frame('Html')->addCss('common/address');
		frame('Html')->addJs('common/address');
		$this->view();
	}
}