<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Index extends HomeBase
{
	public function index()
	{
		html()->addCss();
		html()->addJs();
		html()->addJs('slider');


		$this->assign('_title', distT('title'));
		$this->view(true);
	}
}