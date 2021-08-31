<?php

namespace app\controller\bag;
use app\controller\;

class NewIn extends 
{
	public function index()
	{
		html()->addCss();
		html()->addJs();
		$this->assign('_title', 'Product\'s New In - '.site()->getName());
		$this->view();
	}
}