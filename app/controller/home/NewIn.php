<?php

namespace app\controller\home;
use app\controller\HomeBase;

class NewIn extends HomeBase
{
	public function index()
	{
		html()->addCss();
		html()->addJs();
		
		$this->assign('_title', 'Product\'s New In');
		$this->view(true);
	}
}