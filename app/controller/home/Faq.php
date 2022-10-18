<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Faq extends HomeBase
{
	public function index()
	{
		html()->addCss();
		html()->addJs();
		$this->view(true);
	}
}