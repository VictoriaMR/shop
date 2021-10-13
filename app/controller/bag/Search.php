<?php

namespace app\controller\bag;
use app\controller\Base;
use frame\Html;

class Search extends Base
{
	public function index()
	{	
		$this->assign('_title', appT('search'));
		$this->view();
	}
}