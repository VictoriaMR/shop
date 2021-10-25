<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Search extends HomeBase
{
	public function index()
	{	
		$this->assign('_title', appT('search'));
		$this->view();
	}
}