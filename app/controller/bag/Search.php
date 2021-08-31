<?php

namespace app\controller\bag;

use app\controller\;
use frame\Html;

class Search extends 
{
	public function index()
	{	
		$this->assign('_title', '');
		$this->view();
	}
}