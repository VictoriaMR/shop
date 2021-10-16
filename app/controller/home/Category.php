<?php

namespace app\controller\home;
use app\controller\Base;

class Category extends Base
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();

		$this->view();
	}
}